<?php

namespace App\Console\Commands;

use App\Actions\CompanyManifest\CalculateManifestSummary;
use App\Models\Manifest;
use App\Models\ManifestSummary;
use Illuminate\Console\Command;

class ManifestSummaryGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifest-summary:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Manifest Summary';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CalculateManifestSummary $action)
    {
        $mdlManifest = new Manifest;
        $mdlSummary = new ManifestSummary();

        $listManifest = Manifest::with("manifestFee")
            ->leftJoin($mdlSummary->getTable(), $mdlManifest->qualifyColumn("id"), "=", $mdlSummary->qualifyColumn("manifest_id"))
            ->whereNull($mdlSummary->qualifyColumn("id"))
            ->where($mdlManifest->qualifyColumn("status"), Manifest::STATUS_APPROVED)
            ->select($mdlManifest->qualifyColumn("*"))
            ->get();

        foreach ($listManifest as $manifest) {
            $action->execute($manifest);
        }
    }
}
