<?php

namespace App\Http\Controllers\CompanyManifest;

use App\Actions\CompanyManifest\CalculateTotalPassenger;
use App\Actions\CompanyManifest\CheckTicket;
use App\Actions\CompanyManifest\CreateManifest;
use App\Actions\CompanyManifest\CreateManifestFee;
use App\Actions\CompanyManifest\DownloadManifest;
use App\Actions\CompanyManifest\DownloadManifestApproved;
use App\Actions\CompanyManifest\TakeTicket;
use App\Actions\CompanyManifest\UpdateManifest;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyManifest\FormManifestRequest;
use App\Http\Resources\CompanyManifest\ApprovementResource;
use App\Http\Resources\CompanyManifest\ManifestFormResource;
use App\Http\Resources\CompanyManifest\UserActivityResource;
use App\Http\Resources\CompanyProfile\BoatResource;
use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\RefActivity;
use App\Models\RefDestination;
use App\Models\RefNationality;
use App\Models\User;
use App\Policies\ManifestPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Milon\Barcode\Facades\DNS2DFacade;

class ManifestFormController extends Controller
{

    /**
     * @var ManifestPolicy
     */
    protected $policy;

    public function __construct(ManifestPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function create(Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();
        if (!$this->policy->create($user)) {
            abort(403);
        }

        $company = $user->company->load(
            "boat.fileable",
            "boat.boatman.fileable",
            "boat.boatmanMain",
            "boat.boatmanAsst"
        );

        return Inertia::render('Manifest/Create', [
            "title" => "Create Manifest",
            "additional" => [
                "urlStore" => route("panel.manifest.store"),
                "boats" => $company->boat ?? [],
                "company" => $company,
                "arrInstructor" => $company->boatmanInstructor,
                "arrDivemaster" => $company->boatmanDivemaster,
                "arrGuide" => $company->boatmanGuide,
                "arrDeparture" => RefDestination::query()
                    ->where("type", RefDestination::TYPE_DEPARTURE)
                    ->get(),
                "arrDestination" => RefDestination::query()
                    ->where("type", RefDestination::TYPE_DESTINATION)
                    ->get(),
                "arrNationality" => RefNationality::orderByRaw("
                    CASE
                        WHEN title = 'Malaysian' THEN 1
                        WHEN title = 'China' THEN 2
                        WHEN title = 'Indonesian' THEN 3
                        ELSE 4
                    END ASC, title ASC
                ")->get(),
                "arrActivity" => RefActivity::all(),
                "urlTemplate" => url("assets/passenger-template.xlsx?update_date=2025-06-22"),
                "urlTemplateStaff" => url("assets/staff-template.xlsx?update_date=2025-11-14")
            ]
        ]);
    }

    public function store(FormManifestRequest $request, CreateManifest $action, CreateManifestFee $manifestFeeAction)
    {
        $userAuth = Auth::user();
        if (!$this->policy->create($userAuth)) {
            abort(403);
        }

        $arrData = $request->validated();

        $boat = $userAuth->company->boat()->where("id", $arrData["boat_id"])
            ->first();

        $totalPassenger = (new CalculateTotalPassenger)->execute($arrData);
        $boatCapacity = $boat->capacity ?? 0;

        if ($request->is_rent == false && $boatCapacity < $totalPassenger) {
            throw ValidationException::withMessages([
                "passengers" => 'Total passenger is more than boat capacity!'
            ]);
        }

        // check ticket
        (new CheckTicket)->execute($arrData["passengers"], null);

        $manifest = DB::transaction(function () use ($userAuth, $arrData, $action, $manifestFeeAction) {
            $manifest = $action->execute($arrData, $userAuth);
            $manifestFeeAction->execute($manifest);

            // Auto-approve Seafest Jetty manifests (manual payment)
            if (config('features.bypass_seafest_payment') && $manifest->is_final) {
                $departure = RefDestination::find($manifest->departure_id);

                if ($departure && $departure->code == RefDestination::CODE_SEAFEST) {
                    $manifest->payment_status = Manifest::PAYMENT_STATUS_PAID;

                    $manifest->status = Manifest::STATUS_APPROVED;
                    // $manifest->jetty_approval_status = Manifest::STATUS_APPROVED;

                    $manifest->save();
                }
            }

            if ($manifest->is_final) {
                (new TakeTicket)->execute($manifest);
            }

            return $manifest;
        });

        if ($manifest->is_final) {
            return redirect()->route("panel.manifest.show", $manifest->uuid)
                ->with("message", [
                    "status" => "success",
                    "message" => "Manifest Created!"
                ]);
        }

        return redirect()->route("panel.user-activity.index")
            ->with("message", [
                "status" => "success",
                "message" => "Manifest Created!"
            ]);
    }

    public function edit(Request $request, Manifest $manifest)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();
        if (!$this->policy->update($userAuth, $manifest)) {
            abort(403);
        }

        $company = $manifest->company->load(
            "boat.fileable",
            "boat.boatman.fileable",
            "boat.boatmanMain",
            "boat.boatmanAsst"
        );

        return Inertia::render('Manifest/Edit', [
            "title" => "Update Manifest",
            "additional" => [
                "approvements" => ApprovementResource::collection(
                    $manifest->approvement->sortByDesc('id')->unique('role_id')->values()
                ),
                "manifest" => (new ManifestFormResource($manifest->load("manifestBoatman", "manifestDestination.manifestDestinationActivity", "guest.activities")))->toArray($request),
                "urlUpdate" => route("panel.manifest.update", $manifest->uuid),
                "boats" => $company->boat ?? [],
                "company" => $company,
                "arrInstructor" => $company->boatmanInstructor,
                "arrDivemaster" => $company->boatmanDivemaster,
                "arrGuide" => $company->boatmanGuide,
                "arrDeparture" => RefDestination::query()
                    ->where("type", RefDestination::TYPE_DEPARTURE)
                    ->get(),
                "arrDestination" => RefDestination::query()
                    ->where("type", RefDestination::TYPE_DESTINATION)
                    ->get(),
                "arrNationality" => RefNationality::orderByRaw("
                    CASE
                        WHEN title = 'Malaysian' THEN 1
                        WHEN title = 'China' THEN 2
                        WHEN title = 'Indonesian' THEN 3
                        ELSE 4
                    END ASC, title ASC
                ")->get(),
                "arrActivity" => RefActivity::all(),
                "urlTemplate" => url("assets/passenger-template.xlsx?update_date=2025-06-22"),
                "urlTemplateStaff" => url("assets/staff-template.xlsx?update_date=2025-11-14")
            ]
        ]);
    }

    public function update(FormManifestRequest $request, Manifest $manifest, UpdateManifest $action, CreateManifestFee $manifestFeeAction)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();
        if (!$this->policy->update($userAuth, $manifest)) {
            abort(403);
        }

        $arrData = $request->validated();

        $boat = $userAuth->company->boat()->where("id", $arrData["boat_id"])
            ->first();

        $isChangeDate = $manifest->departure_date != $arrData['departure_date'];

        $totalPassenger = (new CalculateTotalPassenger)->execute($arrData);
        $boatCapacity = $boat->capacity ?? 0;

        if ($request->is_rent == false && $boatCapacity < $totalPassenger) {
            throw ValidationException::withMessages([
                "passengers" => 'Total passenger is more than boat capacity!'
            ]);
        }

        // check ticket
        (new CheckTicket)->execute($arrData["passengers"], $manifest);

        // Prevent reverting finalized manifest to draft
        if ($manifest->is_final == 1 && $arrData['is_final'] == 0) {
            throw ValidationException::withMessages([
                'is_final' => 'Cannot revert finalized manifest to draft.'
            ]);
        }

        // Prevent departure change after payment (only for Jetty Pelancong with PAID fees)
        // Seafest can be changed because it uses manual/bypass payment
        $hasPaidFees = $manifest->manifestFee()
            ->where('status', ManifestFee::STATUS_PAID)
            ->whereNotNull('payment_id') // Only check real online payments
            ->exists();

        if ($hasPaidFees) {
            $oldDeparture = $manifest->departure;
            $isOldDepartureJettyPelancong = $oldDeparture && $oldDeparture->code == RefDestination::CODE_SEMPORNA;

            // Only prevent change if OLD departure was Jetty Pelancong (paid online)
            if ($isOldDepartureJettyPelancong && $manifest->departure_id != $arrData['departure_id']) {
                throw ValidationException::withMessages([
                    'departure_id' => 'Cannot change departure after payment has been made for Jetty Pelancong.'
                ]);
            }
        }

        $manifest = DB::transaction(function () use ($arrData, $action, $manifest, $manifestFeeAction, $isChangeDate) {
            $userAuth = Auth::user();

            $oldDeparture = $manifest->departure;
            $wasSeafest = $oldDeparture && $oldDeparture->code == RefDestination::CODE_SEAFEST;

            // Auto-set payment_status for Seafest before update
            // Check both new is_final value and current manifest state to handle draft->final conversion
            if (config('features.bypass_seafest_payment')) {
                $departure = RefDestination::find($arrData['departure_id']);
                if ($departure && $departure->code == RefDestination::CODE_SEAFEST && $arrData['is_final']) {
                    $arrData['payment_status'] = Manifest::PAYMENT_STATUS_PAID;
                }
            }

            $manifest = $action->execute($manifest, $arrData, $userAuth);

            // Auto-approve Seafest Jetty manifests (manual payment)
            if (config('features.bypass_seafest_payment') && $manifest->is_final) {
                $departure = RefDestination::find($manifest->departure_id);

                if ($departure && $departure->code == RefDestination::CODE_SEAFEST) {
                    $manifest->payment_status = Manifest::PAYMENT_STATUS_PAID;

                    // Seafest manual payment completely bypasses all approvals
                    $manifest->status = Manifest::STATUS_APPROVED;
                    $manifest->jetty_approval_status = Manifest::STATUS_APPROVED;

                    $manifest->save();
                }
            }

            // If departure changed FROM Seafest to another jetty,
            // reset approval statuses — the manifest must go through the proper approval flow.
            if ($wasSeafest) {
                $newDeparture = RefDestination::find($manifest->departure_id);
                $isNowSeafest = $newDeparture && $newDeparture->code == RefDestination::CODE_SEAFEST;

                if (!$isNowSeafest) {
                    $manifest->status = Manifest::STATUS_PENDING;
                    $manifest->jetty_approval_status = Manifest::STATUS_PENDING;
                    $manifest->jetty_approval_comments = null;
                    $manifest->jetty_approval_user_id = null;
                    $manifest->save();
                }
            }

            // Always recalculate when guests change
            // For Seafest, also recalculate PAID fees since it's manual payment
            $manifest->refresh();
            $isSeafest = $manifest->departure && $manifest->departure->code == RefDestination::CODE_SEAFEST;

            // Get fee IDs to delete (PENDING for all, PAID for Seafest or WAS Seafest)
            $feesToDelete = $manifest->manifestFee()
                ->where(function ($query) use ($isSeafest, $wasSeafest) {
                    $query->where('status', ManifestFee::STATUS_PENDING);
                    if ($isSeafest || $wasSeafest) {
                        $query->orWhere(function ($subQuery) {
                            $subQuery->where('status', ManifestFee::STATUS_PAID)
                                ->whereNull('payment_id'); // Only delete if NOT linked to actual payment
                        });
                    }
                })
                ->pluck('id')
                ->toArray();

            // Reset manifest_fee_id for guests linked to fees being deleted
            if (!empty($feesToDelete)) {
                $manifest->guest()
                    ->whereIn('manifest_fee_id', $feesToDelete)
                    ->update(['manifest_fee_id' => null]);
            }

            // Set audit fields before deletion
            $manifest->manifestFee()
                ->whereIn('id', $feesToDelete)
                ->update([
                    'deleted_by' => $userAuth->id,
                    'updated_by' => $userAuth->id
                ]);

            // Delete fees
            $manifest->manifestFee()
                ->whereIn('id', $feesToDelete)
                ->delete();


            $manifestFeeAction->execute($manifest, false, $isChangeDate);

            if ($manifest->is_final) {
                (new TakeTicket)->execute($manifest);
            }

            return $manifest;
        });

        if ($manifest->is_final) {
            return redirect()->route("panel.manifest.show", $manifest->uuid)
                ->with("message", [
                    "status" => "success",
                    "message" => "Manifest Updated!"
                ]);
        }

        return redirect()->route("panel.user-activity.index")
            ->with("message", [
                "status" => "success",
                "message" => "Manifest Updated!"
            ]);
    }

    public function show(Request $request, Manifest $manifest)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();
        if (!$this->policy->view($userAuth, $manifest)) {
            abort(403);
        }

        if ($manifest->jetty_approval_status == Manifest::STATUS_APPROVED) {
            return redirect()->route("panel.manifest.show-approved", [
                "manifest" => $manifest->uuid
            ]);
        }

        $company = $manifest->company->load(
            "boat.fileable",
            "boat.boatman.fileable",
            "boat.boatmanMain",
            "boat.boatmanAsst"
        );

        $authorityRoles = [
            User::ROLE_PDRM,
            User::ROLE_JABATAN_LAUT,
            User::ROLE_SABAH_PARKS,
            User::ROLE_JABATAN_PELABUHAN,
        ];

        $isAuthority = in_array($userAuth->activeRole()->id, $authorityRoles);

        return Inertia::render('Manifest/Show', [
            "title" => "Detail Manifest",
            "additional" => [
                "manifest" => (new ManifestFormResource($manifest->load(
                    "boatmanOther",
                    "manifestFee",
                    "guest.refNationality",
                    "guest.activities",
                    "guest.manifestFee",
                    "departure"
                )))->toArray($request),
                "approvements" => ApprovementResource::collection(
                    $manifest->approvement->sortByDesc('id')->unique('role_id')->values()
                ),
                "canEdit" => $this->policy->update($userAuth, $manifest),
                "urlEdit" => route("panel.manifest.edit", $manifest->uuid),
                "urlPayment" => route("panel.payment.confirmation", ["manifest_id" => [$manifest->id]]),
                "urlActivity" => route("panel.user-activity.index"),
                "urlDownload" => route("panel.manifest.download", $manifest),
                "boats" => BoatResource::collection($company->boat)->toArray($request),
                "company" => $company,
                "isAuthority" => $isAuthority,
                "qrcode" => $manifest->payment_status == Manifest::PAYMENT_STATUS_PAID
                    ? 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($manifest->form_number, 'QRCODE', 33, 33)
                    : false,
                "bypassApproval" => config('features.bypass_approval_flow'),
                "isSeafest" => $manifest->departure && $manifest->departure->code == RefDestination::CODE_SEAFEST,
            ]
        ]);
    }

    public function showApproved(Request $request, Manifest $manifest)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();
        if (!$this->policy->view($userAuth, $manifest)) {
            abort(403);
        }

        if ($manifest->jetty_approval_status != Manifest::STATUS_APPROVED) {
            return redirect()->route("panel.manifest.show", [
                "manifest" => $manifest->uuid
            ]);
        }

        $manifest = $manifest->load([
            "boatmanOther",
            "manifestFee" => function ($query) {
                return $query->whereStatus(1);
            },
            "guest.refNationality",
            "guest.activities"
        ]);
        return Inertia::render('Manifest/ShowApproved', [
            "title" => "Detail Manifest Approved",
            "additional" => [
                "manifest" => (new UserActivityResource($manifest))->toArray($request),
                "approvements" => ApprovementResource::collection(
                    $manifest->approvement->sortBy('id')
                ),
                "urlBack" => route("panel.user-activity.index"),
                "urlDownload" => route("panel.user-activity.download", $manifest),
                "qrcode" => $manifest->payment_status == Manifest::PAYMENT_STATUS_PAID
                    ? 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($manifest->form_number, 'QRCODE', 33, 33)
                    : false,
                "bypassApproval" => config('features.bypass_approval_flow'),
                "isSeafest" => $manifest->departure && $manifest->departure->code == RefDestination::CODE_SEAFEST
            ]
        ]);
    }

    public function download(Manifest $manifest, DownloadManifest $action, DownloadManifestApproved $actionApproved)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();
        if (!$this->policy->view($userAuth, $manifest)) {
            abort(403);
        }

        if ($manifest->jetty_approval_status == Manifest::STATUS_APPROVED) {
            $pdf = $actionApproved->execute($manifest);
        } else {
            $pdf = $action->execute($manifest);
        }

        return $pdf->stream("Receipt Manifest " . $manifest->form_number . ".pdf");
    }
}
