<?php

namespace Database\Seeders;

use App\Models\Boat;
use App\Models\Boatman;
use App\Models\Company;
use App\Models\Fileable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BoatLicenseSeeder extends Seeder
{
    /**
     * Skip boatmen creation to preserve existing production data
     * Set to true to avoid overwriting correct boatmen data with potentially
     * incorrect manual extractions from PDF documents
     */
    protected $skipBoatmen = true;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        info("START SEED " . __CLASS__);

        $this->command->info("Starting boat license import...");

        $boatData = $this->getBoatLicenseData();

        $totalBoatsCreated = 0;
        $totalBoatsUpdated = 0;
        $totalBoatmen = 0;
        $totalFiles = 0;
        $skippedCompanies = [];

        // Group boats by company_id
        $groupedData = [];
        foreach ($boatData as $boat) {
            $groupedData[$boat['company_id']][] = $boat;
        }

        $this->command->info("Processing " . count($groupedData) . " companies");

        foreach ($groupedData as $companyId => $boats) {
            $this->command->info("\n========================================");
            $this->command->info("Processing Company ID: {$companyId}");

            // Find existing company
            $company = Company::find($companyId);

            if (!$company) {
                $this->command->error("Company ID {$companyId} not found! Skipping...");
                $skippedCompanies[] = $companyId;
                continue;
            }

            $this->command->info("Found: {$company->name}");

            foreach ($boats as $boatInfo) {
                $license = $this->normalizeLicense($boatInfo['license']);

                // Check if boat exists
                $existingBoat = $company->boat()
                    ->where('license', $license)
                    ->first();

                $isNewBoat = !$existingBoat;
                $action = $isNewBoat ? 'CREATING' : 'UPDATING';

                // Create or update boat
                $boat = $company->boat()->updateOrCreate(
                    ['license' => $license],
                    [
                        'company_id' => $company->id,
                        'license' => $license,
                        'number' => $license,
                        'capacity' => $boatInfo['capacity'],
                        'license_expiry_date' => $boatInfo['expiry_date'],
                        // 'name' => $license,
                    ]
                );

                if ($isNewBoat) {
                    $totalBoatsCreated++;
                } else {
                    $totalBoatsUpdated++;
                }

                // Create Fileable records for uploaded files
                $filesAttached = 0;
                if (isset($boatInfo['files']) && !empty($boatInfo['files'])) {
                    $filesAttached = $this->createFileableRecords($boat, $boatInfo['files']);
                    $totalFiles += $filesAttached;
                }

                // Create boatmen (use real data if provided, otherwise create dummy)
                // SKIP if $skipBoatmen is true (to preserve production data)
                $boatmenCount = 0;
                if (!$this->skipBoatmen) {
                    if (isset($boatInfo['boatmen']) && !empty($boatInfo['boatmen'])) {
                        $this->createBoatmen($company, $boat, $boatInfo['boatmen']);
                        $boatmenCount = count($boatInfo['boatmen']);
                        $totalBoatmen += $boatmenCount;
                    } else {
                        $this->createDummyBoatmen($company, $boat);
                        $boatmenCount = 2;
                        $totalBoatmen += 2;
                    }
                } else {
                    $this->command->info("Skipping boatmen (preserving existing data)");
                }

                $this->command->info("{$action} Boat: {$license} | Capacity: {$boatInfo['capacity']} | Files: {$filesAttached} | Boatmen: {$boatmenCount}");
            }
        }

        $this->command->info("\n========================================");
        $this->command->info("Import completed!");
        $this->command->info("========================================");
        $this->command->info("Companies processed: " . (count($groupedData) - count($skippedCompanies)));
        $this->command->info("Boats CREATED (new): {$totalBoatsCreated}");
        $this->command->info("Boats UPDATED (existing): {$totalBoatsUpdated}");
        $this->command->info("Total boats processed: " . ($totalBoatsCreated + $totalBoatsUpdated));
        $this->command->info("Files attached: {$totalFiles}");

        if ($this->skipBoatmen) {
            $this->command->info("Boatmen: SKIPPED (preserving existing data)");
        } else {
            $this->command->info("Boatmen created/updated: {$totalBoatmen}");
        }

        if (!empty($skippedCompanies)) {
            $this->command->warn("\n⚠ Skipped company IDs: " . implode(', ', $skippedCompanies));
        }

        info("FINISH SEED " . __CLASS__);
    }

    /**
     * Hardcoded boat license data with boatmen details and file paths
     * Data is loaded from external file for better organization
     */
    protected function getBoatLicenseData(): array
    {
        // BATCH 1: Companies 23, 24, 30, 31, 32, 109, 110
        // return require database_path('seeders/data/boat_license_data.php');

        // BATCH 2: Companies 38, 48, 49, 53, 57, 65, 66
        // return require database_path('seeders/data/boat_license_data_batch2.php');

        // BATCH 3: Companies 77, 79, 83, 90, 98, 101, 104
        return require database_path('seeders/data/boat_license_data_batch3.php');
    }

    /**
     * Create Fileable records for files that are already uploaded to server
     * Deletes old dummy/incorrect files first to prevent duplicates
     */
    protected function createFileableRecords(Boat $boat, array $filePaths): int
    {
        // STEP 1: Delete old dummy/incorrect files for this boat
        // This prevents duplicates and replaces incorrect files
        $deletedCount = Fileable::where('fileable_id', $boat->id)
            ->where('fileable_type', Boat::class)
            ->where('code_type', 'boat_license')
            ->delete();

        if ($deletedCount > 0) {
            $this->command->info("  → Deleted {$deletedCount} old file(s)");
        }

        // STEP 2: Create new correct files
        $filesCreated = 0;

        foreach ($filePaths as $filePath) {
            $fullPath = storage_path("app/{$filePath}");

            // Check if file exists on server
            if (!file_exists($fullPath)) {
                $this->command->warn("File not found: {$filePath}");
                continue;
            }

            // Get file info
            $fileSize = filesize($fullPath);
            $mimeType = mime_content_type($fullPath);
            $fileName = pathinfo($filePath, PATHINFO_FILENAME);

            // Create Fileable record (fresh, no duplicates)
            Fileable::create([
                'fileable_type' => Boat::class,
                'fileable_id' => $boat->id,
                'file' => $filePath,
                'file_name' => $fileName,
                'file_type' => $mimeType,
                'file_size' => $fileSize,
                'code_type' => 'boat_license',
                'access_key' => Str::random(64),
            ]);

            $filesCreated++;
        }

        return $filesCreated;
    }

    /**
     * Normalize license format (keep production format with slashes)
     */
    protected function normalizeLicense(string $license): string
    {
        $normalized = trim($license);

        // Ensure consistent slash format: "SA 4371/5/P" or "SA 4371 / 5 / P"
        // Convert "SA 4371/5/P" to "SA 4371 / 5 / P" (match production format)
        $normalized = str_replace('/', ' / ', $normalized);

        // Clean up multiple spaces
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return $normalized;
    }

    /**
     * Extract license number from license string
     */
    protected function extractLicenseNumber(string $license): string
    {
        if (preg_match('/SA\s*(\d+)/', $license, $matches)) {
            return $matches[1];
        }
        return $license;
    }

    /**
     * Create boatmen from data array (real data from PDF extraction)
     */
    protected function createBoatmen(Company $company, Boat $boat, array $boatmenData): void
    {
        foreach ($boatmenData as $boatmanInfo) {
            $company->boatman()->updateOrCreate(
                [
                    'boat_id' => $boat->id,
                    'type' => $boatmanInfo['type'],
                    'ic_no' => $boatmanInfo['ic_no'],
                ],
                [
                    'boat_id' => $boat->id,
                    'company_id' => $company->id,
                    'name' => $boatmanInfo['name'],
                    'ic_no' => $boatmanInfo['ic_no'],
                    'mate_card' => $boatmanInfo['mate_card'] ?? null,
                    'seaman_card_no' => $boatmanInfo['seaman_card_no'] ?? null,
                    'type' => $boatmanInfo['type'],
                ]
            );
        }
    }

    /**
     * Create dummy boatmen for a boat (fallback for boats without extracted data)
     */
    protected function createDummyBoatmen(Company $company, Boat $boat): void
    {
        // Main boatman
        $company->boatman()->updateOrCreate(
            [
                'boat_id' => $boat->id,
                'type' => Boatman::TYPE_BOATMAN,
                'name' => "Boatman for {$boat->license}",
            ],
            [
                'boat_id' => $boat->id,
                'company_id' => $company->id,
                'name' => "Boatman for {$boat->license}",
                'ic_no' => null,
                'mate_card' => null,
                'seaman_card_no' => null,
                'type' => Boatman::TYPE_BOATMAN,
            ]
        );

        // Assistant boatman
        $company->boatman()->updateOrCreate(
            [
                'boat_id' => $boat->id,
                'type' => Boatman::TYPE_ASSISTANT,
                'name' => "Assistant for {$boat->license}",
            ],
            [
                'boat_id' => $boat->id,
                'company_id' => $company->id,
                'name' => "Assistant for {$boat->license}",
                'ic_no' => null,
                'mate_card' => null,
                'seaman_card_no' => null,
                'type' => Boatman::TYPE_ASSISTANT,
            ]
        );
    }
}
