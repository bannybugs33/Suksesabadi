<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kuitansi', function (Blueprint $table) {
            $table->string('role_pihak')->nullable()->after('nama_pihak');
            $table->date('tanggal_jatuh_tempo')->nullable()->after('tanggal_kuitansi');
            $table->string('bukti_kuitansi')->nullable()->after('keterangan');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('metode_pembayaran')->nullable()->after('nominal');
            $table->string('dicatat_oleh')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'dicatat_oleh']);
        });

        Schema::table('kuitansi', function (Blueprint $table) {
            $table->dropColumn(['role_pihak', 'tanggal_jatuh_tempo', 'bukti_kuitansi']);
        });
    }
};
