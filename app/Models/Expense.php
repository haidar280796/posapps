<?php

namespace App\Models;

use App\Observers\ExpensesObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ExpensesObserver::class])]
class Expense extends Model
{
    protected $fillable = [
        'tanggal',
        'kategori',
        'deskripsi',
        'nominal',
    ];

    public static function kategori()
    {
        return [
            'gaji' => 'Gaji',
            'sewa' => 'Sewa',
            'listrik' => 'Listrik',
        ];
    }

    public function journals()
    {
        return $this->morphMany(Journal::class, 'journalable');
    }
}
