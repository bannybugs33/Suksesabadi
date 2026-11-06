<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catatan pengingat yang sudah terkirim, supaya satu pengingat tidak terkirim dua kali.
     * Kuncinya memuat tanggal jatuh tempo: bila jatuh tempo diubah, pengingat baru boleh terkirim.
     */
    public function up(): void
    {
        Schema::create('pengingat_email', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuitansi_id')->constrained('kuitansi')->cascadeOnDelete();
            $table->string('jenis', 10); // h-7 atau hari-h
            $table->date('tanggal_jatuh_tempo');
            $table->string('penerima');
            $table->timestamp('dikirim_pada');

            $table->unique(['kuitansi_id', 'jenis', 'tanggal_jatuh_tempo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengingat_email');
    }
};
