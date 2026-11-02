<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pisahkan nomor arsip (dibuat sistem, kolom nomor_kuitansi) dari nomor kuitansi fisik
     * (nomor pada dokumen asli, boleh kosong dan tidak harus unik).
     */
    public function up(): void
    {
        Schema::table('kuitansi', function (Blueprint $table) {
            $table->string('nomor_kuitansi_fisik', 50)->nullable()->after('nomor_kuitansi');
        });
    }

    public function down(): void
    {
        Schema::table('kuitansi', function (Blueprint $table) {
            $table->dropColumn('nomor_kuitansi_fisik');
        });
    }
};
