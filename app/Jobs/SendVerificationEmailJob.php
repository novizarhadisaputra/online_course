<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\Auth\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVerificationEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    // Constructor to pass the User to the job
    public function __construct(protected User $user)
    {
        $this->user = $user;
    }

    /**
     * Handle the job (send the email).
     *
     * @return void
     */
    public function handle()
    {
        // Send the verification email
        Mail::to($this->user->email)->send(new VerifyEmail($this->user));
    }
}
