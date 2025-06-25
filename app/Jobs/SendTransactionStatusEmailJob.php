<?php

namespace App\Jobs;

use App\Mail\Transaction\TransactionStatusMail;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransactionStatusEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Transaction $transaction)
    {
        //
    }

    /**
     * Handle the job (send the email).
     *
     * @return void
     */
    public function handle()
    {
        // Send the verification email
        Mail::to($this->transaction->user->email)->send(new TransactionStatusMail($this->transaction));
    }
}
