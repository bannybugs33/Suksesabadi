<?php

namespace Database\Seeders;

use App\Models\Kuitansi;
use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * 10 data kuitansi contoh (utang dan piutang) untuk mengisi sistem.
 *
 * Aman dijalankan di server yang sudah dipakai:
 *  - tidak menyentuh tabel users (tidak ada password yang diubah);
 *  - nomor arsip berawalan "DUM-" sehingga tidak bentrok dengan data asli;
 *  - bisa dijalankan berulang kali tanpa menggandakan data;
 *  - tanggal dihitung dari hari ini, jadi pengingat jatuh tempo ikut "hidup".
 *
 * Jalankan : php artisan db:seed --class=DummyKuitansiSeeder --force
 * Hapus    : php artisan tinker --execute="App\Models\Kuitansi::where('nomor_kuitansi','like','DUM-%')->delete();"
 */
class DummyKuitansiSeeder extends Seeder
{
    private const PIUTANG = 'Pelanggan berutang kepada toko';

    private const UTANG = 'Supplier harus dibayar toko';

    public function run(): void
    {
        $user = User::query()->orderBy('id')->first();

        if (! $user) {
            $this->command?->error('Belum ada user. Buat akun dulu sebelum menjalankan seeder ini.');

            return;
        }

        $hariIni = Carbon::today();

        // Selisih hari dihitung dari hari ini (negatif = sudah lewat).
        $data = [
            [
                'nomor' => 'DUM-001', 'nama' => 'Toko Maju Jaya', 'role' => self::PIUTANG, 'jenis' => 'piutang',
                'nominal' => 4500000, 'kuitansi' => -20, 'tempo' => -9,
                'keterangan' => 'Pesanan beras 50 karung dan gula pasir 20 karung.',
                'bayar' => [],
            ],
            [
                'nomor' => 'DUM-002', 'nama' => 'CV Agro Mandiri', 'role' => self::UTANG, 'jenis' => 'utang',
                'nominal' => 9800000, 'kuitansi' => -15, 'tempo' => -3,
                'keterangan' => 'Pembelian stok tepung terigu dan minyak goreng curah.',
                'bayar' => [[3000000, -15, 'Transfer bank', 'Uang muka (DP) pemesanan']],
            ],
            [
                'nomor' => 'DUM-003', 'nama' => 'Warung Pak Haji', 'role' => self::PIUTANG, 'jenis' => 'piutang',
                'nominal' => 2750000, 'kuitansi' => -12, 'tempo' => 0,
                'keterangan' => 'Belanja bulanan sembako, dibayar bertahap.',
                'bayar' => [
                    [1000000, -12, 'Tunai', 'Pembayaran awal di kasir'],
                    [750000, -5, 'Tunai', 'Cicilan tahap 1'],
                ],
            ],
            [
                'nomor' => 'DUM-004', 'nama' => 'UD Sinar Pagi', 'role' => self::UTANG, 'jenis' => 'utang',
                'nominal' => 6300000, 'kuitansi' => -10, 'tempo' => 1,
                'keterangan' => 'Stok mi instan dan minuman kemasan.',
                'bayar' => [[2000000, -10, 'Transfer bank', 'Uang muka pengiriman']],
            ],
            [
                'nomor' => 'DUM-005', 'nama' => 'Kantin Sekolah Harapan', 'role' => self::PIUTANG, 'jenis' => 'piutang',
                'nominal' => 1850000, 'kuitansi' => -8, 'tempo' => 3,
                'keterangan' => 'Pasokan snack, roti, dan air mineral.',
                'bayar' => [],
            ],
            [
                'nomor' => 'DUM-006', 'nama' => 'PT Indo Sembako', 'role' => self::UTANG, 'jenis' => 'utang',
                'nominal' => 14200000, 'kuitansi' => -6, 'tempo' => 5,
                'keterangan' => 'Pengadaan beras premium 1 ton.',
                'bayar' => [
                    [4000000, -6, 'Transfer bank', 'DP pesanan'],
                    [3000000, -2, 'Transfer bank', 'Cicilan tahap 1'],
                ],
            ],
            [
                'nomor' => 'DUM-007', 'nama' => 'Toko Bangunan Sentosa', 'role' => self::PIUTANG, 'jenis' => 'piutang',
                'nominal' => 5400000, 'kuitansi' => -4, 'tempo' => 7,
                'keterangan' => 'Penjualan grosir perlengkapan dapur.',
                'bayar' => [[1500000, -4, 'Tunai', 'Uang muka']],
            ],
            [
                'nomor' => 'DUM-008', 'nama' => 'Rumah Makan Salero', 'role' => self::PIUTANG, 'jenis' => 'piutang',
                'nominal' => 3200000, 'kuitansi' => -3, 'tempo' => 21,
                'keterangan' => 'Pasokan bumbu dapur dan minyak goreng.',
                'bayar' => [],
            ],
            [
                'nomor' => 'DUM-009', 'nama' => 'Distributor Minyak Nusantara', 'role' => self::UTANG, 'jenis' => 'utang',
                'nominal' => 11600000, 'kuitansi' => -30, 'tempo' => -10,
                'keterangan' => 'Pembelian minyak goreng kemasan 2 liter.',
                'bayar' => [
                    [5000000, -30, 'Transfer bank', 'DP pesanan'],
                    [6600000, -14, 'Transfer bank', 'Pelunasan'],
                ],
            ],
            [
                'nomor' => 'DUM-010', 'nama' => 'Katering Bu Ratna', 'role' => self::PIUTANG, 'jenis' => 'piutang',
                'nominal' => 2100000, 'kuitansi' => -18, 'tempo' => null,
                'keterangan' => 'Penjualan bahan masakan, langsung lunas.',
                'bayar' => [[2100000, -18, 'Tunai', 'Dibayar lunas saat transaksi']],
            ],
        ];

        foreach ($data as $item) {
            $totalTerbayar = (float) array_sum(array_column($item['bayar'], 0));

            $status = Kuitansi::tentukanStatus(
                (float) $item['nominal'],
                $totalTerbayar,
                count($item['bayar'])
            );

            $tempo = $item['tempo'] === null
                ? null
                : $hariIni->copy()->addDays($item['tempo'])->toDateString();

            $kuitansi = Kuitansi::query()->updateOrCreate(
                ['nomor_kuitansi' => $item['nomor']],
                [
                    'user_id' => $user->id,
                    'nama_pihak' => $item['nama'],
                    'role_pihak' => $item['role'],
                    'jenis' => $item['jenis'],
                    'nominal_transaksi' => $item['nominal'],
                    'status_pembayaran' => $status,
                    'tanggal_kuitansi' => $hariIni->copy()->addDays($item['kuitansi'])->toDateString(),
                    'tanggal_jatuh_tempo' => $tempo,
                    'keterangan' => $item['keterangan'],
                ]
            );

            // Hapus lalu buat ulang supaya seeder ini aman dijalankan berulang kali.
            Pembayaran::query()->where('kuitansi_id', $kuitansi->id)->delete();

            foreach ($item['bayar'] as [$nominal, $selisihHari, $metode, $catatan]) {
                Pembayaran::query()->create([
                    'kuitansi_id' => $kuitansi->id,
                    'nominal' => $nominal,
                    'tanggal_pembayaran' => $hariIni->copy()->addDays($selisihHari)->toDateString(),
                    'metode_pembayaran' => $metode,
                    'dicatat_oleh' => $user->name,
                    'catatan' => $catatan,
                ]);
            }
        }

        $this->command?->info('10 kuitansi contoh siap (nomor DUM-001 sampai DUM-010), dicatat atas nama '.$user->name.'.');
    }
}
