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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk', 50)->unique();
            $table->string('nama_produk')->index();
            $table->foreignId('kategori_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('satuan_dasar_id')->constrained('units')->onDelete('cascade');
            $table->string('barcode', 50)->index()->nullable();
            $table->decimal('harga_beli', 10, 2)->default(0);
            $table->decimal('harga_jual', 10, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
