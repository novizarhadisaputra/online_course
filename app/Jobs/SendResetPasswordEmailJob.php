<?php

namespace App\Jobs;

use App\Mail\Auth\ResetPasswordEmail;
use App\Models\User;
use App\Mail\Auth\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendResetPasswordEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    // Constructor to pass the User to the job
    public function __construct(protected User $user) {}

    /**
     * Handle the job (send the email).
     *
     * @return void
     */
    public function handle()
    {
        // Send the verification email
        Mail::to($this->user->email)->send(new ResetPasswordEmail($this->user));
    }
}
