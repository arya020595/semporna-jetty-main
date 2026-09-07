<?php

namespace App\Console\Commands;

use App\Actions\CompanyManifest\CalculateManifestSummary;
use App\Models\Manifest;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class ManifestUuidGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifest:generate-uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Manifest uuid';

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

        $listManifest = Manifest::query()
            ->whereNull("uuid")
            ->select($mdlManifest->qualifyColumn("*"))
            ->get();

        foreach ($listManifest as $manifest) {
            $manifest->update([
                "uuid" => Str::uuid()
            ]);
        }
    }
}
