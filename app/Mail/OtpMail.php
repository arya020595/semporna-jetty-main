<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    protected User $user;
    protected string $otp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Login OTP")
            ->from(config('mail.from.address'), config('app.name'))
            ->view('email.otp', [
                "title" => "Login OTP",
                "name" => $this->user->name,
                "otp" => $this->otp
            ]);
    }
}
