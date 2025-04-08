<?php

use App\Models\Product;
use Illuminate\Support\Str;

function generateUniqueProductCode()
{
    do {
        // Buat kode produk unik, contoh format: PROD-XXXXX
        $code = 'PROD-' . Str::upper(Str::random(10));

        // Cek apakah kode sudah ada di database
        $exists = Product::where('kode_produk', $code)->exists();
    } while ($exists); // Ulangi jika kode sudah ada

    return $code;
}