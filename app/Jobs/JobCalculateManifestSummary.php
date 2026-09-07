<?php

namespace App\Jobs;

use App\Actions\CompanyManifest\CalculateManifestSummary;
use App\Actions\SurveyEntries\TranslateSurveyAnswer;
use App\Models\Manifest;
use App\Models\SurveyEntries;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class JobCalculateManifestSummary implements ShouldQueue
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
    public function handle(CalculateManifestSummary $action)
    {
        $manifest = Manifest::find($this->id);

        $action->execute($manifest);

        Log::info($manifest->number . "||" . now() . "||calculated");
        return "Caclulated";
    }
}
