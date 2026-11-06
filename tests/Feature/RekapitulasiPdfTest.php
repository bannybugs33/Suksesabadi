<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
use App\Models\Pembayaran;
use App\Models\User;
use App\Services\RekapitulasiPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapitulasiPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_download_is_a_real_pdf_file(): void
    {
        $owner = User::factory()->create();
        $kuitansi = Kuitansi::create([
            'user_id' => $owner->id,
            'nomor_kuitansi' => 'KWT-PDF-1',
            'nama_pihak' => 'Warung Bu Sari',
            'jenis' => 'piutang',
            'nominal_transaksi' => 1000000,
            'status_pembayaran' => 'dp',
            'tanggal_kuitansi' => '2026-10-01',
        ]);
        Pembayaran::create(['kuitansi_id' => $kuitansi->id, 'nominal' => 400000, 'tanggal_pembayaran' => '2026-10-02']);

        $response = $this->actingAs($owner)->get(route('rekapitulasi.pdf', ['arah' => 'penerimaan']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', (string) $response->getContent());
        $this->assertStringContainsString(
            'rekapitulasi_semua-periode_penerimaan.pdf',
            (string) $response->headers->get('Content-Disposition')
        );
    }

    public function test_pdf_requires_login(): void
    {
        $this->get(route('rekapitulasi.pdf'))->assertRedirect(route('login'));
    }

    public function test_pdf_contains_rows_and_totals(): void
    {
        $pdf = new RekapitulasiPdf('Toko Sukses Abadi', 'Periode: Semua periode | Arah: Semua arah', 'Dicetak uji', false);

        $isi = $pdf->render(
            ['penerimaan' => 400000.0, 'pengeluaran' => 0.0, 'bersih' => 400000.0, 'jumlah' => 1],
            [[
                'tanggal' => '2 Okt 2026',
                'nama' => 'Warung Bu Sari',
                'nomor' => 'KWT-PDF-1',
                'arah' => 'Penerimaan',
                'metode' => 'Tunai',
                'nominal' => 400000.0,
            ]]
        );

        $this->assertStringStartsWith('%PDF-', $isi);
        $this->assertStringContainsString('Warung Bu Sari', $isi);
        $this->assertStringContainsString('Rp400.000', $isi);
    }

    public function test_pdf_handles_empty_data_and_many_rows(): void
    {
        $kosong = (new RekapitulasiPdf('Toko', 'Filter', 'Dicetak', false))
            ->render(['penerimaan' => 0.0, 'pengeluaran' => 0.0, 'bersih' => 0.0, 'jumlah' => 0], []);
        $this->assertStringContainsString('Belum ada pembayaran', $kosong);

        $baris = array_fill(0, 120, [
            'tanggal' => '2 Okt 2026',
            'nama' => 'Nama pelanggan yang sangat panjang sekali sampai harus dipotong',
            'nomor' => 'KWT-261002-2',
            'arah' => 'Pengeluaran',
            'metode' => 'Transfer',
            'nominal' => 1250000.0,
        ]);
        $banyak = (new RekapitulasiPdf('Toko', 'Filter', 'Dicetak', false))
            ->render(['penerimaan' => 0.0, 'pengeluaran' => 150000000.0, 'bersih' => -150000000.0, 'jumlah' => 120], $baris);
        $this->assertStringStartsWith('%PDF-', $banyak);
        $this->assertStringContainsString('-Rp150.000.000', $banyak);
    }
}
