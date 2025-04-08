<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Journal extends Model
{
    protected $fillable = [
        'tanggal',
        'deskripsi',
        'tipe',
        'jumlah',
        'akun',
        'journalable_id',
        'journalable_type',
    ];

    /**
     * Polymorphic relationship: setiap jurnal bisa terkait dengan sales, payments, atau expenses.
     */
    public function journalable(): MorphTo
    {
        return $this->morphTo();
    }
}
