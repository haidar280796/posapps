<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Journal;

class ExpensesObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        // Pencatatan Pengeluaran
        Journal::create([
            'tanggal' => $expense->tanggal,
            'deskripsi' => 'Pengeluaran - ' . $expense->kategori,
            'tipe' => 'debit',
            'jumlah' => $expense->nominal,
            'akun' => 'Beban ' . $expense->kategori,
            'journalable_id' => $expense->id,
            'journalable_type' => Expense::class,
        ]);

        // Mengurangi Kas
        Journal::create([
            'tanggal' => $expense->tanggal,
            'deskripsi' => 'Pengurangan Kas - ' . $expense->kategori,
            'tipe' => 'kredit',
            'jumlah' => $expense->nominal,
            'akun' => 'Kas',
            'journalable_id' => $expense->id,
            'journalable_type' => Expense::class,
        ]);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        //
    }
}
