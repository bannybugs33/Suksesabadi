<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Satu transaksi harus menghasilkan angka yang sama di Dashboard, Arsip, Jatuh Tempo,
 * dan Rekapitulasi (revisi 10: seluruh fitur saling terhubung).
 */
class KeterhubunganFiturTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-09-29 09:42:00'));
        Storage::fake('local');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function dataKuitansiBaru(array $override = []): array
    {
        return array_merge([
            'tanggal_kuitansi' => '2026-09-29',
            'nama_pihak' => 'Toko Terhubung',
            'jenis' => 'piutang',
            'nominal_transaksi' => '1000000',
            'tanggal_jatuh_tempo' => '2026-10-03',
            'bukti' => UploadedFile::fake()->create('struk.jpg', 100, 'image/jpeg'),
        ], $override);
    }

    public function test_one_transaction_shows_the_same_remaining_balance_in_every_feature(): void
    {
        $owner = User::factory()->create(['name' => 'Andi Saputra']);

        // 1. Buat arsip baru lewat fitur Kuitansi Baru.
        $this->actingAs($owner)->post('/kuitansi/baru', $this->dataKuitansiBaru())->assertRedirect();
        $kuitansi = Kuitansi::where('nama_pihak', 'Toko Terhubung')->firstOrFail();

        // 2. Catat pembayaran sebagian lewat fitur pembayaran.
        $this->actingAs($owner)->post(route('pembayaran.store', $kuitansi), [
            'nominal' => 350000,
            'tanggal_pembayaran' => '2026-09-29',
            'metode_pembayaran' => 'Transfer bank',
        ])->assertRedirect();

        // 3. Sisa Rp650.000 harus sama di semua fitur.
        $this->actingAs($owner)->get('/dashboard')->assertSee('Rp650.000');
        $this->actingAs($owner)->get('/jatuh-tempo?selected='.$kuitansi->id)->assertSee('Rp650.000');
        $this->actingAs($owner)->get('/rekapitulasi')->assertSee('Rp650.000');
        $this->actingAs($owner)->get(route('arsip-kuitansi.show', $kuitansi))->assertSee('Rp650.000');
    }

    public function test_dashboard_remaining_total_matches_rekapitulasi_total(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::query()->first();

        // Sisa piutang + sisa utang di Dashboard = "Belum dibayar" di Rekapitulasi (tanpa filter).
        $this->actingAs($owner)->get('/dashboard')->assertSee('Rp20.850.000');
        $this->actingAs($owner)->get('/rekapitulasi')->assertSee('Rp20.850.000');
    }

    public function test_paid_off_invoice_leaves_dashboard_and_jatuh_tempo_but_stays_in_arsip_history(): void
    {
        $owner = User::factory()->create();
        $this->actingAs($owner)->post('/kuitansi/baru', $this->dataKuitansiBaru(['nama_pihak' => 'Toko Lunas Cepat']))->assertRedirect();
        $kuitansi = Kuitansi::where('nama_pihak', 'Toko Lunas Cepat')->firstOrFail();

        $this->actingAs($owner)->post(route('pembayaran.store', $kuitansi), [
            'nominal' => 1000000,
            'tanggal_pembayaran' => '2026-09-29',
            'metode_pembayaran' => 'Tunai',
        ])->assertRedirect();

        $this->actingAs($owner)->get('/dashboard')->assertDontSee('perlu perhatian mulai H-7');
        $this->actingAs($owner)->get('/jatuh-tempo')->assertDontSee('Toko Lunas Cepat');
        // Riwayat tidak hilang: tetap ada di arsip dan rekap.
        $this->actingAs($owner)->get('/arsip-kuitansi')->assertSee('Toko Lunas Cepat');
        $this->actingAs($owner)->get('/rekapitulasi')->assertSee('Toko Lunas Cepat');
    }

    public function test_fully_paid_new_invoice_is_lunas_with_a_payment_record(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)->post('/kuitansi/baru', $this->dataKuitansiBaru([
            'nama_pihak' => 'Langsung Lunas', 'tanggal_jatuh_tempo' => null, 'nominal_dibayar' => '1000000',
            'tanggal_pembayaran' => '2026-09-29', 'metode_pembayaran' => 'Tunai',
        ]))->assertRedirect();

        $kuitansi = Kuitansi::where('nama_pihak', 'Langsung Lunas')->firstOrFail();
        $this->assertSame(1000000.0, (float) $kuitansi->pembayaran()->sum('nominal'));
        $this->assertSame('lunas', $kuitansi->status_pembayaran);
        // Tidak dihitung sebagai sisa piutang (boleh tetap tampil di Aktivitas terbaru).
        $this->actingAs($owner)->get('/dashboard')->assertSee('Belum dibayar pelanggan · 0 kuitansi');
    }

    public function test_new_invoice_cannot_have_payment_larger_than_total(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)->from('/kuitansi/baru')->post('/kuitansi/baru', $this->dataKuitansiBaru([
            'nominal_dibayar' => '1500000',
            'tanggal_pembayaran' => '2026-09-29',
            'metode_pembayaran' => 'Tunai',
        ]))->assertSessionHasErrors('nominal_dibayar');

        $this->assertDatabaseCount('kuitansi', 0);
    }
}
