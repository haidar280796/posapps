<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    protected $fillable = [
        'user_id',
        'warehouse_id',
        'mulai_shift',
        'selesai_shift',
        'total_transaksi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
