<?php

namespace App\Services;

use App\Models\Kuitansi;
use FPDF;

/**
 * Laporan rekapitulasi pembayaran yang digambar langsung sebagai PDF (bukan konversi dari HTML).
 */
class RekapitulasiPdf extends FPDF
{
    /**
     * Kolom tabel: [judul, lebar (mm), perataan]. Total lebar = 190 mm (A4 dengan margin 10 mm).
     *
     * @var array<int, array{0: string, 1: float, 2: string}>
     */
    private const KOLOM = [
        ['Tanggal', 24.0, 'L'],
        ['Pelanggan / Supplier', 52.0, 'L'],
        ['No. kuitansi', 34.0, 'L'],
        ['Arah', 24.0, 'L'],
        ['Metode', 24.0, 'L'],
        ['Jumlah', 32.0, 'R'],
    ];

    private const TINGGI_BARIS = 7.0;

    private bool $tabelDimulai = false;

    public function __construct(
        private readonly string $namaToko,
        private readonly string $keteranganFilter,
        private readonly string $dicetak,
        bool $kompres = true,
    ) {
        parent::__construct('P', 'mm', 'A4');

        $this->SetMargins(10, 14, 10);
        $this->SetAutoPageBreak(true, 18);
        $this->SetCompression($kompres);
        $this->AliasNbPages();
        $this->SetTitle('Rekapitulasi Pembayaran - '.$namaToko, true);
        $this->SetAuthor('BukuTempo', true);
        $this->SetCreator('BukuTempo', true);
    }

    /**
     * @param  array{penerimaan: float, pengeluaran: float, bersih: float, jumlah: int}  $ringkasan
     * @param  iterable<int, array{tanggal: string, nama: string, nomor: string, arah: string, metode: string, nominal: float}>  $baris
     */
    public function render(array $ringkasan, iterable $baris): string
    {
        $this->AddPage();
        $this->judulLaporan();
        $this->kartuRingkasan($ringkasan);
        $this->tabel($baris);
        $this->catatanKaki();

        return $this->Output('S');
    }

    public function Header(): void
    {
        $this->SetFont('Helvetica', 'B', 11);
        $this->SetTextColor(42, 42, 92);
        $this->Cell(95, 6, self::teks('BukuTempo'), 0, 0, 'L');
        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(107, 107, 134);
        $this->Cell(95, 6, self::teks($this->namaToko), 0, 1, 'R');

        $this->SetDrawColor(220, 219, 214);
        $this->SetLineWidth(0.3);
        $this->Line(10, $this->GetY() + 1, 200, $this->GetY() + 1);
        $this->Ln(5);

        // Halaman lanjutan mengulang judul kolom tabel.
        if ($this->tabelDimulai) {
            $this->barisJudulTabel();
        }
    }

    public function Footer(): void
    {
        $this->SetY(-14);
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(107, 107, 134);
        $this->Cell(120, 5, self::teks($this->dicetak), 0, 0, 'L');
        $this->Cell(70, 5, self::teks('Halaman '.$this->PageNo().' dari {nb}'), 0, 0, 'R');
    }

    private function judulLaporan(): void
    {
        $this->SetFont('Helvetica', 'B', 17);
        $this->SetTextColor(35, 34, 79);
        $this->Cell(0, 9, self::teks('Rekapitulasi Pembayaran'), 0, 1);

        $this->SetFont('Helvetica', '', 9.5);
        $this->SetTextColor(107, 107, 134);
        $this->Cell(0, 5, self::teks($this->keteranganFilter), 0, 1);
        $this->Ln(5);
    }

    /**
     * @param  array{penerimaan: float, pengeluaran: float, bersih: float, jumlah: int}  $ringkasan
     */
    private function kartuRingkasan(array $ringkasan): void
    {
        $kartu = [
            ['Total penerimaan', self::rupiah($ringkasan['penerimaan']), false],
            ['Total pengeluaran', self::rupiah($ringkasan['pengeluaran']), false],
            ['Posisi bersih', self::rupiah($ringkasan['bersih']), true],
            ['Jumlah pembayaran', (string) $ringkasan['jumlah'], false],
        ];

        $lebar = 44.5;
        $jarak = 4.0;
        $tinggi = 19.0;
        $awalX = $this->GetX();
        $awalY = $this->GetY();

        foreach ($kartu as $i => [$label, $nilai, $utama]) {
            $kiri = $awalX + $i * ($lebar + $jarak);

            if ($utama) {
                $this->SetFillColor(42, 42, 92);
            } else {
                $this->SetFillColor(242, 241, 238);
            }
            $this->Rect($kiri, $awalY, $lebar, $tinggi, 'F');

            $this->SetXY($kiri + 3, $awalY + 3);
            $this->SetFont('Helvetica', '', 8);
            if ($utama) {
                $this->SetTextColor(215, 215, 230);
            } else {
                $this->SetTextColor(107, 107, 134);
            }
            $this->Cell($lebar - 6, 4, self::teks($label), 0, 2);

            $this->SetX($kiri + 3);
            $this->SetFont('Helvetica', 'B', 11.5);
            if ($utama) {
                $this->SetTextColor(255, 255, 255);
            } else {
                $this->SetTextColor(35, 34, 79);
            }
            $this->Cell($lebar - 6, 8, self::teks($nilai), 0, 0);
        }

        $this->SetY($awalY + $tinggi + 7);
    }

    /**
     * @param  iterable<int, array{tanggal: string, nama: string, nomor: string, arah: string, metode: string, nominal: float}>  $baris
     */
    private function tabel(iterable $baris): void
    {
        $this->tabelDimulai = true;
        $this->barisJudulTabel();

        $nomor = 0;

        foreach ($baris as $item) {
            if ($this->GetY() + self::TINGGI_BARIS > $this->PageBreakTrigger) {
                $this->AddPage();
            }

            $this->barisData($item, $nomor % 2 === 1);
            $nomor++;
        }

        if ($nomor === 0) {
            $this->SetFont('Helvetica', '', 9.5);
            $this->SetTextColor(107, 107, 134);
            $this->Cell(0, 14, self::teks('Belum ada pembayaran pada filter ini.'), 0, 1, 'C');
        }

        $this->tabelDimulai = false;
    }

    private function barisJudulTabel(): void
    {
        $this->SetFont('Helvetica', 'B', 8.5);
        $this->SetFillColor(42, 42, 92);
        $this->SetTextColor(255, 255, 255);

        foreach (self::KOLOM as $kolom) {
            $this->Cell($kolom[1], 8, self::teks($kolom[0]), 0, 0, $kolom[2], true);
        }

        $this->Ln();
    }

    /**
     * @param  array{tanggal: string, nama: string, nomor: string, arah: string, metode: string, nominal: float}  $item
     */
    private function barisData(array $item, bool $belang): void
    {
        $nilai = [
            $item['tanggal'],
            $item['nama'],
            $item['nomor'],
            $item['arah'],
            $item['metode'],
            self::rupiah($item['nominal']),
        ];

        if ($belang) {
            $this->SetFillColor(247, 246, 243);
        } else {
            $this->SetFillColor(255, 255, 255);
        }

        foreach (self::KOLOM as $i => $kolom) {
            if ($i === 3) {
                $this->SetFont('Helvetica', 'B', 9);
                if ($item['arah'] === 'Penerimaan') {
                    $this->SetTextColor(31, 107, 68);
                } else {
                    $this->SetTextColor(163, 21, 63);
                }
            } else {
                $this->SetFont('Helvetica', $i === 5 ? 'B' : '', 9);
                $this->SetTextColor(35, 34, 79);
            }

            $this->Cell($kolom[1], self::TINGGI_BARIS, self::teks($this->potong($nilai[$i], $kolom[1])), 0, 0, $kolom[2], true);
        }

        $this->Ln();
    }

    private function catatanKaki(): void
    {
        $this->Ln(4);
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(107, 107, 134);
        $this->Cell(0, 5, self::teks('Pembayaran yang dibatalkan tidak dihitung dalam rekapitulasi ini.'), 0, 1);
    }

    /**
     * Potong teks yang terlalu panjang agar tidak keluar dari kolom.
     */
    private function potong(string $teks, float $lebar): string
    {
        $maks = $lebar - 2;

        if ($this->GetStringWidth(self::teks($teks)) <= $maks) {
            return $teks;
        }

        while ($teks !== '' && $this->GetStringWidth(self::teks($teks.'...')) > $maks) {
            $teks = mb_substr($teks, 0, -1);
        }

        return rtrim($teks).'...';
    }

    private static function rupiah(float $angka): string
    {
        return ($angka < 0 ? '-' : '').Kuitansi::rupiah(abs($angka));
    }

    /**
     * Font bawaan PDF memakai Windows-1252, jadi teks UTF-8 dikonversi dulu.
     */
    private static function teks(string $teks): string
    {
        return mb_convert_encoding($teks, 'Windows-1252', 'UTF-8');
    }
}
