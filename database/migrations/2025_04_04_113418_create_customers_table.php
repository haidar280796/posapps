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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->index();
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        // Tambahkan kolom customer_id ke sales
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::dropIfExists('customers');
    }
};
