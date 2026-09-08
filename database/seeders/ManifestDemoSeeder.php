<?php

namespace Database\Seeders;

use App\Actions\CompanyManifest\ApprovementManifest;
use App\Actions\CompanyManifest\CreateManifest;
use App\Actions\CompanyManifest\CreateManifestFee;
use App\Models\Approvement;
use App\Models\Boatman;
use App\Models\Company;
use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\Payment;
use App\Models\RefDestination;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeds two demo Manifest trips end-to-end (guests, fees, approvals,
 * payment) by driving the same Actions the real app uses, so the panel's
 * "Pending" and "Approved" views have real data to click through.
 *
 * Not part of the default db:seed chain — run explicitly:
 *   php artisan db:seed --class=ManifestDemoSeeder
 */
class ManifestDemoSeeder extends Seeder
{
    const LOCAL_NATIONALITY_ID = 18; // Taiwanese — matches CreateManifestFee::NATIONALITY_LOCAL
    const FOREIGN_NATIONALITY_ID = 8; // Chinese

    public function run()
    {
        $company = Company::first();
        if (!$company) {
            $this->call(DummyCompanySeeder::class);
            $company = Company::first();
        }

        if (Manifest::where('company_id', $company->id)->exists()) {
            $this->command->info('ManifestDemoSeeder skipped: company already has manifests.');
            return;
        }

        $agent = User::where('email', 'company@email.com')->first();
        $boat = $company->boat()->first();
        $boatman = $company->boatman()->where('type', Boatman::TYPE_BOATMAN)->first();
        $assistant = $company->boatman()->where('type', Boatman::TYPE_ASSISTANT)->first();

        $departure = RefDestination::where('code', RefDestination::CODE_SEMPORNA)->first();
        $destinations = RefDestination::where('type', RefDestination::TYPE_DESTINATION)
            ->whereNotIn('title', ['Bohey Dulang', 'Sibuan', 'Mantabuan', 'Maiga']) // avoid Sabah Parks approval requirement
            ->orderBy('title')
            ->limit(2)
            ->pluck('id');
        $activityIds = [1, 2]; // Snorkeling, Fun Dive / Diving

        $pdrm = $this->approverUser('pdrm@email.com', 'Demo PDRM', User::ROLE_PDRM);
        $laut = $this->approverUser('jabatanlaut@email.com', 'Demo Jabatan Laut', User::ROLE_JABATAN_LAUT);
        $pelabuhan = $this->approverUser('jabatanpelabuhan@email.com', 'Demo Jabatan Pelabuhan', User::ROLE_JABATAN_PELABUHAN);
        $jettyOperator = $this->approverUser('jettyoperator@email.com', 'Demo Jetty Operator', User::ROLE_OPERATOR_JETTY);

        $baseData = [
            'is_rent' => false,
            'departure_time' => '08:00',
            'company_id' => $company->id,
            'company_name' => $company->name,
            'boat_id' => $boat->id,
            'boat_number' => $boat->number,
            'boatman_id' => $boatman->id,
            'boatman_name' => $boatman->name,
            'boatman_mate_no' => $boatman->mate_card,
            'seaman_no' => $boatman->seaman_card_no,
            'boatman_ic_no' => $boatman->ic_no,
            'assistant_id' => $assistant->id,
            'assistant_name' => $assistant->name,
            'assistant_mate_no' => $assistant->mate_card,
            'assistant_ic_no' => $assistant->ic_no,
            'assistant_seaman_no' => $assistant->seaman_card_no,
            'is_dive_activity' => 1,
            'departure_id' => $departure->id,
            'destination' => $destinations->toArray(),
            'destination_activity' => $destinations->map(fn ($id) => [
                'destination_id' => $id,
                'activity' => $activityIds,
            ])->toArray(),
            'instructor' => [],
            'divemaster' => [],
            'guide' => [],
            'is_final' => 1,
            'passengers' => $this->demoPassengers($activityIds),
            'staff' => [],
        ];

        // Manifest A: submitted, awaiting authority approval, unpaid.
        $manifestA = (new CreateManifest)->execute(
            array_merge($baseData, ['departure_date' => now()->addDays(5)->format('Y-m-d')]),
            $agent
        );
        (new CreateManifestFee)->execute($manifestA);

        // Manifest B: fully approved by every authority + Jetty Operator, and paid.
        $manifestB = (new CreateManifest)->execute(
            array_merge($baseData, ['departure_date' => now()->addDays(6)->format('Y-m-d')]),
            $agent
        );
        (new CreateManifestFee)->execute($manifestB);

        // ApprovementManifest::checkApprove() reads the `approvement` relation off
        // whatever $manifest instance it's given. In the real app each approval is
        // a separate HTTP request, so that's always a freshly-loaded model with no
        // stale relation cache. Here we call it three times in a row, so a fresh
        // instance is re-fetched before each call to reproduce that same behavior
        // — reusing $manifestB across calls would leave a stale, one-row cache and
        // the manifest stuck at "In Progress" no matter how many approvals land.
        $approvementAction = new ApprovementManifest;
        foreach ([$pdrm, $laut, $pelabuhan] as $approver) {
            $approvementAction->execute(Manifest::find($manifestB->id), $approver, [
                'status' => Approvement::STATUS_APPROVED,
                'comments' => 'Approved for demo',
            ]);
        }
        $manifestB = Manifest::find($manifestB->id);

        $manifestB->update([
            'jetty_approval_status' => Manifest::STATUS_APPROVED,
            'jetty_approval_comments' => 'Cleared by Jetty Operator (demo)',
            'jetty_approval_user_id' => $jettyOperator->id,
        ]);

        $fee = $manifestB->manifestFee()->where('status', ManifestFee::STATUS_PENDING)->first();
        if ($fee) {
            $payment = Payment::create([
                'user_id' => $agent->id,
                'code' => (string) Str::uuid(),
                'reciept_number' => 'DEMO-' . $manifestB->form_number,
                'payment_method' => 'demo',
                'first_name' => $agent->name,
                'email' => $agent->email,
                'tel_no' => '0123456789',
                'amount' => $fee->total,
                'status' => Payment::STATUS_PAID,
                'merchant_id' => 'demo',
                'detail' => (string) $manifestB->id,
            ]);

            $fee->update(['status' => ManifestFee::STATUS_PAID, 'payment_id' => $payment->id]);
            DB::table('manifest_payment')->insert([
                'manifest_id' => $manifestB->id,
                'payment_id' => $payment->id,
            ]);
            $manifestB->update(['payment_status' => Manifest::PAYMENT_STATUS_PAID]);
        }

        $this->command->info("Created demo manifests: {$manifestA->form_number} (pending), {$manifestB->form_number} (approved & paid)");
    }

    protected function approverUser(string $email, string $name, int $role): User
    {
        $user = User::updateOrCreate(['email' => $email], [
            'name' => $name,
            'code' => '',
            'staf_id' => '',
            'tel_no' => '',
            'email' => $email,
            'password' => Hash::make('abc12345'),
        ]);

        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }

        return $user;
    }

    protected function demoPassengers(array $activityIds): array
    {
        return [
            [
                'name' => 'Ahmad Bin Ismail',
                'ic_no' => 'A1234567',
                'nationality_id' => self::LOCAL_NATIONALITY_ID,
                'nationality_name' => 'Taiwanese',
                'age' => 34,
                'gender' => 'M',
                'next_of_kin' => 'Siti Binti Ismail',
                'emergency_contact' => '0134567890',
                'activity_ids' => $activityIds,
                'is_stay_resort' => 0,
            ],
            [
                'name' => 'Wei Ling Tan',
                'ic_no' => 'B7654321',
                'nationality_id' => self::FOREIGN_NATIONALITY_ID,
                'nationality_name' => 'Chinese',
                'age' => 9,
                'gender' => 'F',
                'next_of_kin' => 'Mei Ling Tan',
                'emergency_contact' => '0129876543',
                'activity_ids' => $activityIds,
                'is_stay_resort' => 0,
            ],
        ];
    }
}
