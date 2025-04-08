<?php

namespace App\Models;

use App\Observers\SalesObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([SalesObserver::class])]
class Sale extends Model
{
    protected $fillable = [
        'no_invoice',
        'cashier_shift_id',
        'total',
        'metode_penjualan',
        'metode_pembayaran',
        'status',
        'customer_id',
    ];

    public static function metodePenjualan()
    {
        return [
            'walk_in_customer' => 'Walk In Customer',
            'online_order' => 'Online Order',
        ];
    }

    public static function metodePembayaran()
    {
        return [
            'cash' => 'Cash',
            'qris' => 'QRIS',
        ];
    }

    public static function status()
    {
        return [
            'pending' => 'Pending',
            'in_process' => 'In Process',
            'completed' => 'Completed',
            'canceled' => 'Canceled',
        ];
    }

    public function items()
    {
        return $this->hasMany(SaleDetail::class, 'sales_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'sales_id')->where('status', 'paid');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function journals()
    {
        return $this->morphMany(Journal::class, 'journalable');
    }
}
