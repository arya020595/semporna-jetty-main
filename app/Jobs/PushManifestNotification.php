<?php

namespace App\Jobs;

use App\Models\Manifest;
use App\Models\User;
use App\Models\UserOnesignal;
use Berkayk\OneSignal\OneSignalFacade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class PushManifestNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $manifestId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($manifestId)
    {
        $this->manifestId = $manifestId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $manifest = Manifest::find($this->manifestId);

        if (!$manifest) {
            Log::info("manifest Not Found : (id:{$manifest->form_number})");
            return "User Not Found";
        }

        // get user authorites with onesignal id exists
        $arrUid = UserOnesignal::query()
            ->whereHas("user", function ($query) {
                return $query->whereHas("roles", function ($query) {
                    return $query->whereIn("id", [
                        User::ROLE_JABATAN_LAUT,
                        User::ROLE_JABATAN_PELABUHAN,
                        User::ROLE_PDRM,
                        User::ROLE_SABAH_PARKS
                    ]);
                });
            })->get()
            ->pluck("uid");

        $message = "Manifest Number: " . $manifest->form_number . ",\n" .
            "Company : " . $manifest->company_name;
        $url = route('panel.autho-my-dashboard.show', ["manifest" => $manifest->uuid]);

        OneSignalFacade::addParams([
            "headings" => ["en" => "New Manifest"]
        ])->sendNotificationToUser(
            $message,
            $arrUid->toArray(),
            $url
        );

        return "Notification Sent";
    }
}
