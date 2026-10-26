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

    public function test_rekapitulasi_page_renders_successfully(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $response = $this->actingAs($user)->get('/rekapitulasi');

        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Utang dan Piutang');
        $response->assertSee('BukuTempo');
    }

    public function test_rekapitulasi_displays_kuitansi_and_calculated_totals(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $kuitansiPiutang = Kuitansi::create([
            'user_id' => $user->id,
            'nomor_kuitansi' => 'KUI-TEST-001',
            'nama_pihak' => 'Pelanggan A',
            'jenis' => 'piutang',
            'nominal_transaksi' => 1000000,
            'status_pembayaran' => 'cicilan',
            'tanggal_kuitansi' => '2026-10-01',
        ]);

        Pembayaran::create([
            'kuitansi_id' => $kuitansiPiutang->id,
            'nominal' => 400000,
            'tanggal_pembayaran' => '2026-10-02',
        ]);

        $response = $this->actingAs($user)->get('/rekapitulasi');

        $response->assertStatus(200);
        $response->assertSee('KUI-TEST-001');
        $response->assertSee('Pelanggan A');
        $response->assertSee('Rp1.000.000');
        $response->assertSee('Rp400.000');
        $response->assertSee('Rp600.000');
    }

    public function test_rekapitulasi_filters_by_jenis(): void
    {
        $user = User::factory()->create(['id' => 1]);

        Kuitansi::create([
            'user_id' => $user->id,
            'nomor_kuitansi' => 'KUI-PIUTANG-1',
            'nama_pihak' => 'Pihak Piutang',
            'jenis' => 'piutang',
            'nominal_transaksi' => 500000,
            'status_pembayaran' => 'lunas',
            'tanggal_kuitansi' => '2026-10-01',
        ]);

        Kuitansi::create([
            'user_id' => $user->id,
            'nomor_kuitansi' => 'KUI-UTANG-1',
            'nama_pihak' => 'Pihak Utang',
            'jenis' => 'utang',
            'nominal_transaksi' => 700000,
            'status_pembayaran' => 'belum_lunas',
            'tanggal_kuitansi' => '2026-10-01',
        ]);

        $response = $this->actingAs($user)->get('/rekapitulasi?jenis=piutang');

        $response->assertStatus(200);
        $response->assertSee('KUI-PIUTANG-1');
        $response->assertDontSee('KUI-UTANG-1');
    }
}
