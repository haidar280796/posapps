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
        Schema::create('account_receivables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->constrained('sales')->onDelete('cascade');
            $table->decimal('total_piutang', 15, 2);
            $table->decimal('sisa_piutang', 15, 2);
            $table->enum('status', ['belum lunas', 'lunas sebagian', 'lunas'])->default('belum lunas');
            $table->date('jatuh_tempo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_receivables');
    }
};
