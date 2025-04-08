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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->constrained('sales')->onDelete('cascade');
            $table->decimal('total_transaksi', 10,2);
            $table->decimal('total_bayar', 10,2)->nullable()->default(0);
            $table->decimal('kembalian', 10,2)->nullable()->default(0);
            $table->string('qris_url')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'failed', 'canceled'])->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
