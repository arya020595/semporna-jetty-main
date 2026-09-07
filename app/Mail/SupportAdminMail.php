<?php

namespace App\Mail;

use App\Models\Support;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    protected Support $support;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Support $support)
    {
        $this->support = $support;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Support Email | " . $this->support->subject)
            ->from(config('mail.from.address'), config('app.name'))
            ->view('email.supportAdmin', [
                "title" => "Support Email",
                "name" => config('mail.admin.name', "Admin"),
                "from" => $this->support->from,
                "email" => $this->support->email,
                "subject" => $this->support->subject,
                "messageText" => $this->support->description
            ]);
    }
}
