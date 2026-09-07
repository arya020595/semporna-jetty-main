<?php

namespace App\Http\Controllers\Authorities;

use App\Actions\Authorities\GetManifestActivityDatatables;
use App\Actions\CompanyManifest\ApprovementManifest;
use App\Actions\CompanyManifest\DownloadManifest;
use App\Actions\CompanyManifest\DownloadManifestApproved;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyManifest\ActivitySearchRequest;
use App\Http\Requests\CompanyManifest\ApprovementRequest;
use App\Http\Resources\Authorities\ManifestResource;
use App\Http\Resources\CompanyManifest\ApprovementResource;
use App\Http\Resources\CompanyManifest\UserActivityResource;
use App\Http\Resources\CompanyProfile\BoatResource;
use App\Jobs\JobCalculateManifestSummary;
use App\Models\Approvement;
use App\Models\Manifest;
use App\Models\RefDestination;
use App\Models\User;
use App\Policies\Authorities\MyDashboardPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Milon\Barcode\Facades\DNS2DFacade;

class MyDashboardController extends Controller
{
    protected MyDashboardPolicy $policy;

    public function __construct(MyDashboardPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function index(ActivitySearchRequest $request, GetManifestActivityDatatables $action)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->viewAny($userAuth)) {
            abort(403);
        }

        $sessionKey = 'authority_dashboard_filters';

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

        return Inertia::render('Authorities/MyDashboard/Index', [
            "title" => "Activity",
            "additional" => [
                "data" => ManifestResource::collection($data),
                "columns" => $action->getColumns(),
                "filters" => $filters,
                "urlIndex" => route("panel.autho-my-dashboard.index"),
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

        $viewComponent = $manifest->status == Manifest::STATUS_APPROVED
            ? "Authorities/MyDashboard/ShowApproved"
            : "Authorities/MyDashboard/Show";

        return Inertia::render($viewComponent, [
            "title" => "Detail Manifest",
            "additional" => [
                "manifest" => (new UserActivityResource($manifest->load("boatmanOther", "manifestFee", "guest.refNationality", "guest.activities")))->toArray($request),
                "urlBack" => route("panel.autho-my-dashboard.index"),
                "urlApprove" => route("panel.autho-my-dashboard.approve", ["manifest" => $manifest->uuid]),
                "urlDownload" => route("panel.autho-my-dashboard.download", ["manifest" => $manifest->uuid]),
                "canApprove" => $this->policy->approve($userAuth, $manifest),
                "boats" => BoatResource::collection($company->boat)->toArray($request),
                "company" => $company,
                "isAuthority" => $isAuthority,
                "approvements" => ApprovementResource::collection(
                    $manifest->status == Manifest::STATUS_APPROVED
                        ? $manifest->approvement->where(
                            "status",
                            Approvement::STATUS_APPROVED
                        )->sortByDesc('id')->unique('role_id')->values()
                        : $manifest->approvement->sortByDesc('id')->unique('role_id')->values()
                ),
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
        if (!$this->policy->approve($userAuth, $manifest)) {
            abort(403);
        }

        $approvement = DB::transaction(function () use (
            $userAuth,
            $request,
            $manifest,
            $action
        ) {
            $arrRequest = $request->validated();
            $approvement = $action->execute($manifest, $userAuth, $arrRequest);

            if ($manifest->status == Manifest::STATUS_APPROVED) {
                JobCalculateManifestSummary::dispatch($manifest->id);
            }

            return $approvement;
        });

        return redirect()->route("panel.autho-my-dashboard.index")
            ->with("message", [
                "status" => "success",
                "message" => "Manifest " . $approvement->status_text . "!"
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

        DB::transaction(function () use ($manifest) {
            $manifest->manifestBoatman()->delete();
            $manifest->manifestDestination()->delete();
            $manifest->delete();
        });

        return redirect()->route("panel.autho-my-dashboard.index")
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
