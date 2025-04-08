<?php

namespace App\Observers;

use App\Models\Journal;
use App\Models\Payment;

class PaymentsObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        if ($payment->status === 'paid') {
            // Penerimaan Kas
            Journal::create([
                'tanggal' => $payment->created_at,
                'deskripsi' => 'Pembayaran Piutang - Sales ID: ' . $payment->sales_id,
                'tipe' => 'debit',
                'jumlah' => $payment->total_transaksi,
                'akun' => 'Kas',
                'journalable_id' => $payment->id,
                'journalable_type' => Payment::class,
            ]);

            // Mengurangi Piutang
            Journal::create([
                'tanggal' => $payment->created_at,
                'deskripsi' => 'Pengurangan Piutang - Sales ID: ' . $payment->sales_id,
                'tipe' => 'kredit',
                'jumlah' => $payment->total_transaksi,
                'akun' => 'Piutang Dagang',
                'journalable_id' => $payment->id,
                'journalable_type' => Payment::class,
            ]);
        }
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
