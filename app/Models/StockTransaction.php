<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'warehouse_id',
        'product_id',
        'jumlah',
        'satuan_id',
    ];

    function user() {
        return $this->belongsTo(User::class);
    }

    function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }

    function product() {
        return $this->belongsTo(Product::class);
    }

    function unit() {
        return $this->belongsTo(Unit::class, 'satuan_id');
    }
}
