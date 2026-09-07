<?php

namespace App\Jobs;

use App\Mail\NotificationMail;
use App\Mail\OtpMail;
use App\Models\Notifable;
use App\Models\Otpable;
use App\Models\Template;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class SendEmailOtp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $otpId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($otpId)
    {
        $this->otpId = $otpId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dataOtp = Otpable::find($this->otpId);

        $user = User::where("email", $dataOtp->address)->first();

        if (!$user) {
            Log::info("User Not Found : (id:{$dataOtp->address})");
            return "User Not Found";
        }

        $email = config("app.env") == "production"
            ? $user->email
            : config("mail.test_email");

        Mail::to($email)
            ->send(new OtpMail($user, $dataOtp->otp));

        return "Email Sent";
    }
}
