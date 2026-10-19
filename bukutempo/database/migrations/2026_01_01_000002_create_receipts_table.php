<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();               // contoh: KWT-240912
            $table->foreignId('party_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['receivable', 'payable']); // piutang pelanggan / utang supplier
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->date('issued_at');
            $table->date('due_date');
            $table->enum('status', ['belum_lunas', 'dp', 'cicilan', 'lunas'])->default('belum_lunas');
            $table->timestamp('settled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
