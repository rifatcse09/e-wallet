<?php

namespace App\Listeners;

use App\Events\TransactionProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTransactionNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TransactionProcessed  $event
     * @return void
     */
    public function handle(TransactionProcessed $event)
    {
        $transaction = $event->transaction;

        Mail::to($transaction->receiver->email)->send(new \App\Mail\TransactionProcessed($transaction));
    }
}
