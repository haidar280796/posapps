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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('deskripsi');
            $table->enum('tipe', ['debit', 'kredit']);
            $table->decimal('jumlah', 15, 2);
            $table->string('akun'); // Contoh: 'Kas', 'Piutang Dagang', 'Pendapatan'
            $table->morphs('journalable'); // Bisa terkait dengan sales, payments, atau expenses
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
