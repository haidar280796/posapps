<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'kategori_id',
        'satuan_dasar_id',
        'barcode',
        'harga_beli',
        'harga_jual',
        'deskripsi',
        'image',
    ];

    protected function hargaBeli(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => intval($value),
        );
    }

    protected function hargaJual(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => intval($value),
        );
    }

    function satuanDasar()
    {
        return $this->belongsTo(Unit::class, 'satuan_dasar_id');
    }

    function stock()
    {
        $warehouseId = session('warehouse_user'); // Ambil warehouse_id dari session
        return $this->hasOne(Stock::class, 'product_id')->where('warehouse_id', $warehouseId);
    }

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function productPricings()
    {
        $warehouseId = session('warehouse_user');
        return $this->hasMany(ProductPrice::class)->where('warehouse_id', $warehouseId);
    }
}
