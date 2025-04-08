<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'warehouse_id',
        'product_id',
        'jumlah',
    ];

    function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }

    function product() {
        return $this->belongsTo(Product::class);
    }
}
