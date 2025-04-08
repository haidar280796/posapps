<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'satuan_id',
        'konversi',
        'harga_beli',
        'harga_jual',
        'barcode',
    ];

    protected function hargaBeli(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => intval($value),
        );
    }

    protected function hargaJual(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => intval($value),
        );
    }

    protected function konversi(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => intval($value),
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Unit::class, 'satuan_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
