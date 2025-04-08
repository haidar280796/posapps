<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'warehouse_id',
        'warehouse_target_id',
        'product_id',
        'user_id',
        'adjustment_type',
        'jumlah',
        'satuan_id',
        'reason'
    ];

    public static function adjustmentTypes() {
        return [
            'expired' => 'Expired', 
            'transfer' => 'Transfer', 
            'lost' => 'Lost', 
            'damaged' => 'Damaged'
        ];
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouseTarget()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_target_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function unit() {
        return $this->belongsTo(Unit::class, 'satuan_id');
    }
}
