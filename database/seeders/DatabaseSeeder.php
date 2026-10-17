<?php

namespace Database\Seeders;

use App\Models\Kuitansi;
use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        $user = User::query()->findOrFail(1);

        $ref = Carbon::today();

        $daftarKuitansi = [
            [
                'nomor_kuitansi' => 'KWT-240912',
                'nama_pihak' => 'Toko Makmur Jaya',
                'role_pihak' => 'Pelanggan berutang kepada toko',
                'jenis' => 'piutang',
                'nominal_transaksi' => 6250000,
                'status_pembayaran' => 'belum_lunas',
                'tanggal_kuitansi' => $ref->copy()->subDays(17)->format('Y-m-d'),
                'tanggal_jatuh_tempo' => $ref->copy()->subDays(7)->format('Y-m-d'),
                'keterangan' => 'Tagihan pesanan grosir bahan sembako dan minyak goreng.',
                'pembayaran' => [],
            ],
            [
                'nomor_kuitansi' => 'KWT-240915',
                'nama_pihak' => 'PT Sumber Pangan',
                'role_pihak' => 'Supplier harus dibayar toko',
                'jenis' => 'utang',
                'nominal_transaksi' => 12400000,
                'status_pembayaran' => 'dp',
                'tanggal_kuitansi' => $ref->copy()->subDays(14)->format('Y-m-d'),
                'tanggal_jatuh_tempo' => $ref->copy()->subDays(4)->format('Y-m-d'),
                'keterangan' => 'Pengadaan stok beras premium dan gula pasir karungan.',
                'pembayaran' => [
                    [
                        'nominal' => 4000000,
                        'tanggal_pembayaran' => $ref->copy()->subDays(14)->format('Y-m-d'),
                        'metode_pembayaran' => 'Transfer bank',
                        'dicatat_oleh' => 'Andi Saputra',
                        'catatan' => 'Uang muka (DP) awal pengiriman',
                    ],
                ],
            ],
            [
                'nomor_kuitansi' => 'KWT-240918',
                'nama_pihak' => 'Warung Bu Sari',
                'role_pihak' => 'Pelanggan berutang kepada Toko Tempo',
                'jenis' => 'piutang',
                'nominal_transaksi' => 8750000,
                'status_pembayaran' => 'cicilan',
                'tanggal_kuitansi' => $ref->copy()->subDays(11)->format('Y-m-d'),
                'tanggal_jatuh_tempo' => $ref->copy()->subDays(1)->format('Y-m-d'),
                'keterangan' => 'Pembelian 25 dus minyak goreng kemasan 1 liter. Pembayaran disepakati dalam dua tahap.',
                'bukti_kuitansi' => 'kuitansi-warung-bu-sari.jpg',
                'pembayaran' => [
                    [
                        'nominal' => 3000000,
                        'tanggal_pembayaran' => $ref->copy()->subDays(11)->format('Y-m-d'),
                        'metode_pembayaran' => 'Tunai - pembayaran awal',
                        'dicatat_oleh' => 'Andi Saputra',
                        'catatan' => 'Pembayaran awal di kasir',
                    ],
                    [
                        'nominal' => 2000000,
                        'tanggal_pembayaran' => $ref->copy()->format('Y-m-d'),
                        'metode_pembayaran' => 'Transfer bank',
                        'dicatat_oleh' => 'Andi Saputra',
                        'catatan' => 'Cicilan tahap 1 via transfer bank',
                    ],
                ],
            ],
            [
                'nomor_kuitansi' => 'KWT-240905',
                'nama_pihak' => 'Kantin Melati',
                'role_pihak' => 'Pelanggan berutang kepada toko',
                'jenis' => 'piutang',
                'nominal_transaksi' => 3450000,
                'status_pembayaran' => 'dp',
                'tanggal_kuitansi' => $ref->copy()->subDays(24)->format('Y-m-d'),
                'tanggal_jatuh_tempo' => $ref->copy()->addDay()->format('Y-m-d'),
                'keterangan' => 'Pasokan snack, biskuit, dan minuman kemasan botol.',
                'pembayaran' => [
                    [
                        'nominal' => 1000000,
                        'tanggal_pembayaran' => $ref->copy()->subDays(24)->format('Y-m-d'),
                        'metode_pembayaran' => 'Tunai - DP',
                        'dicatat_oleh' => 'Andi Saputra',
                        'catatan' => 'Uang muka pesanan',
                    ],
                ],
            ],
            [
                'nomor_kuitansi' => 'KUI-2026-001',
                'nama_pihak' => 'Toko Berkah Jaya',
                'role_pihak' => 'Pelanggan',
                'jenis' => 'piutang',
                'nominal_transaksi' => 15000000,
                'status_pembayaran' => 'lunas',
                'tanggal_kuitansi' => $ref->copy()->subDays(5)->format('Y-m-d'),
                'tanggal_jatuh_tempo' => $ref->copy()->addDays(10)->format('Y-m-d'),
                'keterangan' => 'Penjualan grosir sembako lunas.',
                'pembayaran' => [
                    [
                        'nominal' => 15000000,
                        'tanggal_pembayaran' => $ref->copy()->subDays(4)->format('Y-m-d'),
                        'metode_pembayaran' => 'Transfer bank',
                        'dicatat_oleh' => 'Andi Saputra',
                        'catatan' => 'Pelunasan transfer bank',
                    ],
                ],
            ],
        ];

        foreach ($daftarKuitansi as $data) {
            $pembayaranList = $data['pembayaran'];
            unset($data['pembayaran']);

            $kuitansi = Kuitansi::query()->updateOrCreate(
                ['nomor_kuitansi' => $data['nomor_kuitansi']],
                array_merge($data, ['user_id' => $user->id])
            );

            foreach ($pembayaranList as $bayar) {
                Pembayaran::query()->firstOrCreate(
                    [
                        'kuitansi_id' => $kuitansi->id,
                        'nominal' => $bayar['nominal'],
                        'tanggal_pembayaran' => $bayar['tanggal_pembayaran'],
                    ],
                    $bayar
                );
            }
        }
    }
}
