<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KuitansiBaruTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_kuitansi_baru(): void
    {
        $response = $this->get('/kuitansi/baru');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_kuitansi_baru_form(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-29 09:42:00'));

        $user = User::factory()->create([
            'name' => 'Andi Saputra',
            'email' => 'andi@suksesabadi.com',
        ]);

        $response = $this->actingAs($user)->get('/kuitansi/baru');

        $response->assertStatus(200);
        $response->assertSee('Tambah Kuitansi Baru');
        $response->assertSee('Lengkapi data transaksi agar tempo dan status pembayaran dapat dipantau.');
        $response->assertSee('Informasi transaksi');
        $response->assertSee('Bukti & pembayaran', false);
        $response->assertSee('Simpan arsip');
        $response->assertSee('Informasi kuitansi');
        $response->assertSee('Tanggal kuitansi *');
        $response->assertSee('Nama pelanggan / supplier *', false);
        $response->assertSee('Jenis kuitansi *');
        $response->assertSee('Nominal transaksi *');
        $response->assertSee('Catatan transaksi');
        $response->assertSee('Pembayaran & jatuh tempo', false);
        $response->assertSee('Status pembayaran awal *');
        $response->assertSee('Nominal dibayar');
        $response->assertSee('Tanggal jatuh tempo *');
        $response->assertSee('Pengingat');
        $response->assertSee('Unggah foto kuitansi');
        $response->assertSee('Tarik foto ke sini');
        $response->assertSee('RINGKASAN ARSIP');
        $response->assertSee('KWT-260929');
        $response->assertSee('Simpan kuitansi');

        Carbon::setTestNow();
    }

    public function test_user_can_create_new_kuitansi_without_file(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/kuitansi/baru', [
            'tanggal_kuitansi' => '2026-09-29',
            'nama_pihak' => 'Warung Bu Sari',
            'jenis' => 'piutang',
            'nominal_transaksi' => 'Rp 500.000',
            'catatan' => 'Minyak goreng 2 dus',
            'status_pembayaran' => 'belum_lunas',
            'nominal_dibayar' => 'Rp 0',
            'tanggal_jatuh_tempo' => '2026-10-13',
        ]);

        $this->assertDatabaseHas('kuitansi', [
            'user_id' => $user->id,
            'nama_pihak' => 'Warung Bu Sari',
            'jenis' => 'piutang',
            'role_pihak' => 'Pelanggan',
            'nominal_transaksi' => 500000,
            'status_pembayaran' => 'belum_lunas',
            'keterangan' => 'Minyak goreng 2 dus',
        ]);

        $kuitansi = Kuitansi::where('nama_pihak', 'Warung Bu Sari')->first();
        $this->assertNotNull($kuitansi);
        // Tanggal dibandingkan lewat model (cast 'date'): di SQLite nilainya tersimpan
        // sebagai '2026-09-29 00:00:00', di MySQL sebagai '2026-09-29'.
        $this->assertSame('2026-09-29', $kuitansi->tanggal_kuitansi->toDateString());
        $this->assertSame('2026-10-13', $kuitansi->tanggal_jatuh_tempo->toDateString());
        $this->assertStringStartsWith('KWT-260929', $kuitansi->nomor_kuitansi);

        $response->assertRedirect(route('arsip-kuitansi.show', $kuitansi->id));
        $response->assertSessionHas('status');
    }

    public function test_user_can_create_kuitansi_with_initial_dp_payment(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/kuitansi/baru', [
            'tanggal_kuitansi' => '2026-09-29',
            'nama_pihak' => 'PT Sumber Pangan',
            'jenis' => 'utang',
            'nominal_transaksi' => 'Rp 2.000.000',
            'status_pembayaran' => 'dp',
            'nominal_dibayar' => 'Rp 550.000',
            'tanggal_jatuh_tempo' => '2026-10-15',
        ]);

        $kuitansi = Kuitansi::where('nama_pihak', 'PT Sumber Pangan')->first();
        $this->assertNotNull($kuitansi);
        $this->assertSame('utang', $kuitansi->jenis);
        $this->assertSame('Supplier', $kuitansi->role_pihak);
        $this->assertSame('dp', $kuitansi->status_pembayaran);

        $this->assertDatabaseHas('pembayaran', [
            'kuitansi_id' => $kuitansi->id,
            'nominal' => 550000,
        ]);

        $pembayaran = Pembayaran::where('kuitansi_id', $kuitansi->id)->first();
        $this->assertNotNull($pembayaran);
        $this->assertSame('2026-09-29', $pembayaran->tanggal_pembayaran->toDateString());

        $response->assertRedirect(route('arsip-kuitansi.show', $kuitansi->id));
    }

    public function test_user_can_upload_kuitansi_receipt_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('struk_pembelian.jpg', 600, 800);

        $response = $this->actingAs($user)->post('/kuitansi/baru', [
            'tanggal_kuitansi' => '2026-09-29',
            'nama_pihak' => 'Toko Berkah Abadi',
            'jenis' => 'piutang',
            'nominal_transaksi' => '150000',
            'status_pembayaran' => 'belum_lunas',
            'tanggal_jatuh_tempo' => '2026-10-10',
            'bukti' => $file,
        ]);

        $kuitansi = Kuitansi::where('nama_pihak', 'Toko Berkah Abadi')->first();
        $this->assertNotNull($kuitansi);
        $this->assertNotNull($kuitansi->bukti_kuitansi);

        // Path should start with storage/kuitansi/
        $this->assertStringStartsWith('storage/kuitansi/', $kuitansi->bukti_kuitansi);

        $relativePath = str_replace('storage/', '', $kuitansi->bukti_kuitansi);
        Storage::disk('public')->assertExists($relativePath);

        $response->assertRedirect(route('arsip-kuitansi.show', $kuitansi->id));
    }

    public function test_validation_fails_for_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/kuitansi/baru', [
            'tanggal_kuitansi' => '',
            'nama_pihak' => '',
            'nominal_transaksi' => '0',
        ]);

        $response->assertSessionHasErrors(['tanggal_kuitansi', 'nama_pihak', 'nominal_transaksi', 'status_pembayaran', 'tanggal_jatuh_tempo']);
    }
}
