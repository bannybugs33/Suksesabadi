<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapitulasiTest extends TestCase
{
    use RefreshDatabase;

    private function buatKuitansi(User $user, string $nomor, string $nama, string $jenis, int $nominal): Kuitansi
    {
        return Kuitansi::create([
            'user_id' => $user->id,
            'nomor_kuitansi' => $nomor,
            'nama_pihak' => $nama,
            'jenis' => $jenis,
            'nominal_transaksi' => $nominal,
            'status_pembayaran' => 'cicilan',
            'tanggal_kuitansi' => '2026-10-01',
        ]);
    }

    public function test_rekapitulasi_page_renders_successfully(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $response = $this->actingAs($user)->get('/rekapitulasi');

        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi');
        $response->assertSee('BukuTempo');
        $response->assertSee('Belum ada pembayaran');
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('jatuh-tempo.index'));
        $response->assertSee(route('arsip-kuitansi.index'));
        $response->assertSee(route('kuitansi.create'));
        $response->assertSee(route('rekapitulasi.index'));
    }

    public function test_rekapitulasi_displays_payments_and_calculated_totals(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $kuitansi = $this->buatKuitansi($user, 'KUI-TEST-001', 'Pelanggan A', 'piutang', 1000000);

        Pembayaran::create([
            'kuitansi_id' => $kuitansi->id,
            'nominal' => 400000,
            'tanggal_pembayaran' => '2026-10-02',
        ]);

        $response = $this->actingAs($user)->get('/rekapitulasi');

        $response->assertStatus(200);
        $response->assertSee('KUI-TEST-001');
        $response->assertSee('Pelanggan A');
        $response->assertSee('Rp400.000');
        $response->assertSee('Penerimaan');
    }

    public function test_rekapitulasi_filters_by_arah(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $piutang = $this->buatKuitansi($user, 'KUI-PIUTANG-1', 'Pihak Piutang', 'piutang', 500000);
        $utang = $this->buatKuitansi($user, 'KUI-UTANG-1', 'Pihak Utang', 'utang', 700000);

        Pembayaran::create(['kuitansi_id' => $piutang->id, 'nominal' => 200000, 'tanggal_pembayaran' => '2026-10-02']);
        Pembayaran::create(['kuitansi_id' => $utang->id, 'nominal' => 300000, 'tanggal_pembayaran' => '2026-10-03']);

        $response = $this->actingAs($user)->get('/rekapitulasi?arah=penerimaan');

        $response->assertStatus(200);
        $response->assertSee('KUI-PIUTANG-1');
        $response->assertDontSee('KUI-UTANG-1');
    }

    public function test_cancelled_payment_is_not_counted(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $kuitansi = $this->buatKuitansi($user, 'KUI-BATAL-1', 'Pelanggan B', 'piutang', 1000000);

        Pembayaran::create([
            'kuitansi_id' => $kuitansi->id,
            'nominal' => 250000,
            'tanggal_pembayaran' => '2026-10-02',
            'status' => 'dibatalkan',
        ]);

        $this->actingAs($user)->get('/rekapitulasi')
            ->assertOk()
            ->assertDontSee('KUI-BATAL-1')
            ->assertSee('Belum ada pembayaran');
    }
}
