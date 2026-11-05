<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuitansi', function (Blueprint $table) {
            if (! Schema::hasColumn('kuitansi', 'dibatalkan_pada')) {
                $table->timestamp('dibatalkan_pada')->nullable();
            }
            if (! Schema::hasColumn('kuitansi', 'alasan_pembatalan')) {
                $table->text('alasan_pembatalan')->nullable();
            }
            if (! Schema::hasColumn('kuitansi', 'uang_dikembalikan')) {
                // null = tidak ada uang diselesaikan; true = dikembalikan penuh; false = tidak dikembalikan.
                $table->boolean('uang_dikembalikan')->nullable();
            }
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayaran', 'status')) {
                $table->string('status', 20)->default('aktif');
            }
            if (! Schema::hasColumn('pembayaran', 'alasan_pembatalan')) {
                $table->text('alasan_pembatalan')->nullable();
            }
            if (! Schema::hasColumn('pembayaran', 'dibatalkan_pada')) {
                $table->timestamp('dibatalkan_pada')->nullable();
            }
        });

        // Tabel lama (skema berbeda, ada kolom "jenis") dibuang lalu dibuat ulang sesuai kode saat ini.
        Schema::dropIfExists('koreksi_kuitansi');

        Schema::create('koreksi_kuitansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuitansi_id')->constrained('kuitansi')->cascadeOnDelete();
            $table->foreignId('pembayaran_id')->nullable()->constrained('pembayaran')->nullOnDelete();
            $table->string('ringkasan');
            $table->text('detail')->nullable();
            $table->text('alasan')->nullable();
            $table->string('dicatat_oleh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koreksi_kuitansi');
    }
};
