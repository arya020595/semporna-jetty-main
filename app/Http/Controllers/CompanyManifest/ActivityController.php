<?php

namespace App\Http\Controllers\CompanyManifest;

use App\Actions\CompanyManifest\ApprovementManifest;
use App\Actions\CompanyManifest\CreateManifestFee;
use App\Actions\CompanyManifest\DownloadManifest;
use App\Actions\CompanyManifest\DownloadManifestApproved;
use App\Actions\CompanyManifest\GetManifestActivity;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyManifest\ActivitySearchRequest;
use App\Http\Requests\CompanyManifest\ApprovementRequest;
use App\Http\Resources\CompanyManifest\ApprovementResource;
use App\Http\Resources\CompanyManifest\ManifestResource;
use App\Http\Resources\CompanyManifest\UserActivityResource;
use App\Http\Resources\CompanyProfile\BoatResource;
use App\Jobs\JobCalculateManifestSummary;
use App\Models\Manifest;
use App\Models\RefDestination;
use App\Models\User;
use App\Policies\UserActivityPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Milon\Barcode\Facades\DNS2DFacade;

class ActivityController extends Controller
{

    protected UserActivityPolicy $policy;

    public function __construct(UserActivityPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function index(ActivitySearchRequest $request, GetManifestActivity $action)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->viewAny($userAuth)) {
            abort(403);
        }

        $sessionKey = 'user_activity_filters';

        $filterKeys = [
            'search_fields',
            'search_values',
            'page',
            'per_page',
            'order_by',
            'order_type'
        ];

        $hasFiltersInRequest = $request->anyFilled($filterKeys) || $request->hasAny($filterKeys);

        if (!$hasFiltersInRequest && $request->session()->has($sessionKey)) {
            $savedFilters = array_filter($request->session()->get($sessionKey));

            if (!empty($savedFilters)) {
                return redirect()->route($request->route()->getName(), $savedFilters);
            }
        }

        $filters = $request->validated();
        $data = $action->execute($filters);

        if (!empty(array_filter($filters))) {
            $request->session()->put($sessionKey, $filters);
        } else {
            $request->session()->forget($sessionKey);
        }

        return Inertia::render('UserActivity/Index', [
            "title" => "Activity",
            "additional" => [
                "data" => ManifestResource::collection($data),
                "columns" => $action->getColumns(),
                "filters" => $filters,
                "urlIndex" => route("panel.user-activity.index"),
            ]
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

        $viewComponent = $manifest->jetty_approval_status == Manifest::STATUS_APPROVED
            ? "UserActivity/ShowApproved"
            : "UserActivity/Show";

        return Inertia::render($viewComponent, [
            "title" => "Detail Manifest",
            "additional" => [
                "manifest" => (new UserActivityResource($manifest->load("boatmanOther", "manifestFee", "guest.refNationality", "guest.activities")))->toArray($request),
                "urlBack" => route("panel.user-activity.index"),
                "urlApprove" => route("panel.user-activity.approve", ["manifest" => $manifest->uuid]),
                "urlDownload" => route("panel.user-activity.download", ["manifest" => $manifest->uuid]),
                "canApprove" => $this->policy->approve($userAuth, $manifest)
                    || $this->policy->jettyApproval($userAuth, $manifest),
                "boats" => BoatResource::collection($company->boat)->toArray($request),
                "company" => $company,
                "isAuthority" => $isAuthority,
                "approvements" => ApprovementResource::collection($manifest->approvement->sortByDesc('id')->unique('role_id')->values()),
                "qrcode" => $manifest->payment_status == Manifest::PAYMENT_STATUS_PAID
                    ? 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($manifest->form_number, 'QRCODE', 33, 33)
                    : false,
                "bypassApproval" => config('features.bypass_approval_flow'),
                "isSeafest" => $manifest->departure && $manifest->departure->code == RefDestination::CODE_SEAFEST
            ]
        ]);
    }

    public function approve(ApprovementRequest $request, Manifest $manifest, ApprovementManifest $action)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();
        if (!$this->policy->approve($userAuth, $manifest) && !$this->policy->jettyApproval($userAuth, $manifest)) {
            abort(403);
        }

        $approvement = DB::transaction(function () use (
            $userAuth,
            $request,
            $manifest,
            $action
        ) {
            $arrRequest = $request->validated();
            if ($this->policy->jettyApproval($userAuth, $manifest)) {
                $manifest->update([
                    "jetty_approval_status" => $arrRequest["status"],
                    "jetty_approval_comments" => $arrRequest["comments"],
                    "jetty_approval_user_id" => $userAuth->id
                ]);

                if ($manifest->jetty_approval_status == Manifest::STATUS_APPROVED) {
                    JobCalculateManifestSummary::dispatch($manifest->id);
                }

                $status = $manifest->jetty_approval_status_text;
            } else {
                $approvement = $action->execute($manifest, $userAuth, $arrRequest);
                $status = $approvement->status_text;
            }

            return [
                "status" => $status
            ];
        });

        return redirect()->route("panel.user-activity.index")
            ->with("message", [
                "status" => "success",
                "message" => "Manifest " . $approvement["status"] . "!"
            ]);
    }

    public function destroy(Manifest $manifest)
    {
        /**
         * @var User
         */
        $userAuth = Auth::user();
        if (!$this->policy->delete($userAuth, $manifest)) {
            abort(403);
        }

        DB::transaction(function () use ($manifest, $userAuth) {
            $manifest->manifestBoatman()->delete();
            $manifest->manifestDestination()->delete();
            $manifest->manifestFee()->delete();
            $manifest->guest()->delete();
            $manifest->deleted_by = $userAuth->id;
            $manifest->save();

            $manifest->delete();

            // Trigger recalculation for concurrent manifests for the same boat/date
            $concurrentManifests = Manifest::query()
                ->where(function ($query) use ($manifest) {
                    if ($manifest->boat_id) {
                        $query->where('boat_id', $manifest->boat_id);
                    } else {
                        $query->whereNull('boat_id')
                            ->where('boat_number', $manifest->boat_number);
                    }
                })
                ->whereDate('departure_date', $manifest->departure_date)
                ->where('id', '!=', $manifest->id)
                ->where('status', '!=', Manifest::STATUS_REJECTED)
                ->get();

            foreach ($concurrentManifests as $otherManifest) {
                (new CreateManifestFee)->execute($otherManifest);
            }
        });

        return redirect()->route("panel.user-activity.index")
            ->with("message", [
                "status" => "success",
                "message" => "Manifest Deleted!"
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

        if ($manifest->status == Manifest::STATUS_APPROVED) {
            $pdf = $actionApproved->execute($manifest);
        } else {
            $pdf = $action->execute($manifest);
        }

        return $pdf->stream("Receipt Manifest " . $manifest->form_number . ".pdf");
    }
}
