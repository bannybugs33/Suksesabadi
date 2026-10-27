<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Party;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::create([
            'name' => 'Andi Saputra',
            'username' => 'andi.owner',
            'role' => 'owner',
            'store_name' => 'Toko Tempo',
            'password' => 'password',
        ]);

        // Pembuat kuitansi; tanggal jatuh tempo dihitung relatif terhadap hari ini
        $make = function (string $code, string $party, string $partyType, string $type,
            int $total, int $paid, int $dueOffset, string $status): Receipt {
            $p = Party::firstOrCreate(['name' => $party], ['type' => $partyType]);

            return Receipt::create([
                'code' => $code,
                'party_id' => $p->id,
                'type' => $type,
                'total_amount' => $total,
                'paid_amount' => $paid,
                'issued_at' => today()->subDays(20),
                'due_date' => today()->addDays($dueOffset),
                'status' => $status,
                'settled_at' => $status === 'lunas' ? now() : null,
            ]);
        };

        // Piutang pelanggan (total sisa Rp24.450.000)
        $make('KWT-240912', 'Toko Makmur Jaya', 'customer', 'receivable', 6250000, 0, -7, 'belum_lunas');
        $buSari = $make('KWT-240918', 'Warung Bu Sari', 'customer', 'receivable', 5750000, 2000000, -1, 'cicilan');
        $make('KWT-240905', 'Kantin Melati', 'customer', 'receivable', 2450000, 0, 1, 'belum_lunas');
        $make('KWT-240920', 'Toko Berkah', 'customer', 'receivable', 9000000, 3000000, 10, 'dp');
        $make('KWT-240921', 'Depot Sinar', 'customer', 'receivable', 6000000, 0, 20, 'belum_lunas');

        // Utang supplier (total sisa Rp20.800.000)
        $payable = $make('KWT-240915', 'PT Sumber Pangan', 'supplier', 'payable', 8400000, 0, -4, 'belum_lunas');
        $make('KWT-240922', 'CV Grosir Pusat', 'supplier', 'payable', 10000000, 3000000, 9, 'cicilan');
        $make('KWT-240923', 'UD Kemasan Prima', 'supplier', 'payable', 5400000, 0, 15, 'belum_lunas');

        // 7 kuitansi lunas bulan ini (total Rp18.200.000)
        $settled = null;
        foreach (range(1, 7) as $i) {
            $settled = $make("KWT-24080$i", 'CV Segar Abadi', 'customer', 'receivable', 2600000, 2600000, -5, 'lunas');
        }

        // Data grafik 6 bulan terakhir
        $in = [8200000, 13000000, 11500000, 12800000, 9800000, 10200000];
        $out = [5900000, 9500000, 10500000, 8200000, 7000000, 6800000];
        foreach (range(0, 5) as $i) {
            $date = $i === 5
                ? now()
                : now()->startOfMonth()->subMonths(5 - $i)->addDays(10);

            Payment::create(['receipt_id' => $settled->id, 'amount' => $in[$i], 'paid_at' => $date]);
            Payment::create(['receipt_id' => $payable->id, 'amount' => $out[$i], 'paid_at' => $date]);
        }

        // Aktivitas terbaru
        $log = function (string $type, string $title, string $desc, $when, ?Receipt $receipt = null) use ($owner) {
            $activity = new Activity([
                'user_id' => $owner->id,
                'receipt_id' => $receipt?->id,
                'type' => $type,
                'title' => $title,
                'description' => $desc,
            ]);
            $activity->created_at = $when;
            $activity->save();
        };

        $log('payment_recorded', 'Pembayaran cicilan dicatat', 'Warung Bu Sari • Rp2.000.000', now()->setTime(9, 18), $buSari);
        $log('receipt_created', 'Kuitansi baru ditambahkan', 'PT Sumber Pangan • Rp12.400.000', now()->subDay()->setTime(16, 42), $payable);
        $log('receipt_settled', 'Kuitansi ditandai lunas', 'CV Segar Abadi • Rp5.800.000', now()->subDays(9)->setTime(11, 5), $settled);
    }
}
