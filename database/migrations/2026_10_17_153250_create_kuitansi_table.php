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
        Schema::create('kuitansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nomor_kuitansi')->unique();
            $table->string('nama_pihak');
            $table->enum('jenis', ['utang', 'piutang']);
            $table->decimal('nominal_transaksi', 15, 2);
            $table->enum('status_pembayaran', ['belum_lunas', 'dp', 'cicilan', 'lunas'])->default('belum_lunas');
            $table->date('tanggal_kuitansi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuitansi');
    }
};
