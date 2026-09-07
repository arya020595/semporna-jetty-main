<?php

namespace App\Jobs;

use App\Actions\SurveyEntries\TranslateSurveyAnswer;
use App\Models\SurveyEntries;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TranslateEntries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $entriesId)
    {
        $this->id = $entriesId;
    }

    /**
     * Execute the job.
     *
     * @return string
     */
    public function handle(TranslateSurveyAnswer $actionTranslate)
    {
        $entries = SurveyEntries::find($this->id);
        if ($entries->lang == "en") {
            Log::info($entries->number . "||" . $entries->lang . "||" . now() . "||already english");
            return "Default Lang Already English";
        }

        DB::transaction(function () use ($entries, $actionTranslate) {
            $actionTranslate->execute($entries);
            $entries->update([
                "is_translated" => 1
            ]);
        });

        Log::info($entries->number . "||" . $entries->lang . "||" . now() . "||translated to english");
        return "Transalted";
    }
}
