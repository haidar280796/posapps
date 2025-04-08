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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('satuan_id')->constrained('units');
            $table->decimal('jumlah', 10,2);
            $table->decimal('harga', 10,2);
            $table->decimal('harga_modal', 10,2)->nullable();
            $table->decimal('harga_diskon', 10,2)->nullable();
            $table->decimal('subtotal', 15,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
