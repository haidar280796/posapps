<?php

namespace App\Models;

use App\Observers\PaymentsObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([PaymentsObserver::class])]
class Payment extends Model
{
    protected $fillable = [
        'sales_id',
        'total_transaksi',
        'total_bayar',
        'kembalian',
        'status',
        'qris_url'
    ];

    public static function statuses()
    {
        return [
            'unpaid' => 'Unpaid',
            'paid' => 'Paid',
            'canceled' => 'Canceled',
            'failed' => 'Failed',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function journals()
    {
        return $this->morphMany(Journal::class, 'journalable');
    }
}
