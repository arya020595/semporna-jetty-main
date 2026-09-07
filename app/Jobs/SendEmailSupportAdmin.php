<?php

namespace App\Jobs;

use App\Mail\SupportAdminMail;
use App\Mail\TestMail;
use App\Models\Support;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class SendEmailSupportAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;

    protected Support $support;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $email, int $supportId)
    {
        $this->email = $email;
        $this->support = Support::find($supportId);

        if (!$this->support) {
            Log::alert("Support Data Not Found! id:$supportId");
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->support) {
            return;
        }

        Mail::to($this->email)
            ->send(new SupportAdminMail($this->support));
    }
}
