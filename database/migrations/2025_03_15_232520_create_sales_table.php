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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice', 20)->unique();
            $table->foreignId('cashier_shift_id')->constrained('cashier_shifts');
            $table->decimal('total', 15,2);
            $table->enum('metode_penjualan', ['walk_in_customer', 'online_order']);
            $table->enum('metode_pembayaran', ['cash', 'qris']);
            $table->enum('status', ['pending', 'in_process', 'completed', 'canceled']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
