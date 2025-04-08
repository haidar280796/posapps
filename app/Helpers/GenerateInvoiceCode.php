<?php

use App\Models\Sale;
use Illuminate\Support\Str;

function generateInvoiceCode()
{
    do {
        // Buat no invoice unik, contoh format: INV-XXXXX
        $code = 'INV-' . Str::upper(Str::random(12));

        // Cek apakah kode sudah ada di database
        $exists = Sale::where('no_invoice', $code)->exists();
    } while ($exists); // Ulangi jika kode sudah ada

    return $code;
}