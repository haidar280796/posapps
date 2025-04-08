<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    protected $fillable = [
        'sales_id',
        'total_piutang',
        'sisa_piutang',
        'status',
        'jatuh_tempo',
    ];

    public static function status() {
        return [
            'belum lunas' => 'belum lunas',
            'lunas sebagian' => 'lunas sebagian',
            'lunas' => 'lunas',
        ];
    }
}
