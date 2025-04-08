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
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('satuan_id')->constrained('units')->onDelete('cascade');
            $table->decimal('konversi', 10, 2);
            $table->decimal('harga_jual', 10, 2);
            $table->string('barcode', 50)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
