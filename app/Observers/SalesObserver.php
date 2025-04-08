<?php

namespace App\Observers;

use App\Models\Journal;
use App\Models\Sale;

class SalesObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        // Pencatatan Pendapatan
        Journal::create([
            'tanggal' => $sale->created_at,
            'deskripsi' => 'Penjualan Produk - ' . $sale->no_invoice,
            'tipe' => 'kredit',
            'jumlah' => $sale->total,
            'akun' => 'Pendapatan',
            'journalable_id' => $sale->id,
            'journalable_type' => Sale::class,
        ]);

        // Jika metode pembayaran 'cash', catat ke Kas
        if ($sale->metode_pembayaran === 'cash') {
            Journal::create([
                'tanggal' => $sale->created_at,
                'deskripsi' => 'Penerimaan Kas - ' . $sale->no_invoice,
                'tipe' => 'debit',
                'jumlah' => $sale->total,
                'akun' => 'Kas',
                'journalable_id' => $sale->id,
                'journalable_type' => Sale::class,
            ]);
        } else {
            // Jika metode pembayaran 'qris', masuk sebagai piutang dulu
            Journal::create([
                'tanggal' => $sale->created_at,
                'deskripsi' => 'Piutang Dagang - ' . $sale->no_invoice,
                'tipe' => 'debit',
                'jumlah' => $sale->total,
                'akun' => 'Piutang Dagang',
                'journalable_id' => $sale->id,
                'journalable_type' => Sale::class,
            ]);
        }
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "restored" event.
     */
    public function restored(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "force deleted" event.
     */
    public function forceDeleted(Sale $sale): void
    {
        //
    }
}
