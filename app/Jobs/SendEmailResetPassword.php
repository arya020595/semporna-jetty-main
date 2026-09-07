<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


class SendEmailResetPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $token;
    private $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $email, string $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::where('email', $this->email)
            ->first();

        $email = config("app.env") == "production"
            ? $this->email
            : config("mail.test_email");

        Mail::to($email)
            ->send(new ResetPasswordMail($user, $this->token));
    }
}
