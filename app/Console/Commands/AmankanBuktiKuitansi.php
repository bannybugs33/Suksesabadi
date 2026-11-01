<?php

namespace App\Console\Commands;

use App\Models\Kuitansi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AmankanBuktiKuitansi extends Command
{
    protected $signature = 'bukti:amankan';

    protected $description = 'Pindahkan foto kuitansi lama dari folder publik (storage/kuitansi) ke penyimpanan privat';

    public function handle(): int
    {
        $dipindah = 0;

        Kuitansi::query()
            ->where('bukti_kuitansi', 'like', 'storage/%')
            ->each(function (Kuitansi $kuitansi) use (&$dipindah): void {
                $relatif = substr((string) $kuitansi->bukti_kuitansi, strlen('storage/'));

                if ($relatif === '' || str_contains($relatif, '..')) {
                    return;
                }

                $publik = Storage::disk('public');
                $privat = Storage::disk(Kuitansi::DISK_BUKTI);

                if ($publik->exists($relatif)) {
                    if (! $privat->exists($relatif)) {
                        $privat->put($relatif, $publik->get($relatif));
                    }
                    $publik->delete($relatif);
                } elseif (! $privat->exists($relatif)) {
                    // Berkas tidak ada di mana pun; biarkan data apa adanya.
                    $this->warn("Berkas tidak ditemukan untuk {$kuitansi->nomor_kuitansi}: {$relatif}");

                    return;
                }

                $kuitansi->forceFill(['bukti_kuitansi' => $relatif])->save();
                $dipindah++;
            });

        $this->info("Selesai. {$dipindah} foto kuitansi diamankan.");

        return self::SUCCESS;
    }
}
