<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $fillable = [
        'sales_id',
        'product_id',
        'satuan_id',
        'jumlah',
        'harga',
        'harga_modal',
        'harga_diskon',
        'subtotal',
    ];

    public function sale() {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'satuan_id');
    }
}
