<?php

namespace App\Http\Controllers\CompanyManifest;

use App\Actions\CompanyManifest\DownloadManifest;
use App\Actions\CompanyManifest\DownloadManifestApproved;
use App\Actions\CompanyManifest\GetPaymentHistory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyManifest\PaymentHistorySearchRequest;
use App\Http\Resources\CompanyManifest\ManifestFormResource;
use App\Http\Resources\CompanyManifest\PaymentHistoryResource;
use App\Models\Manifest;
use App\Models\RefDestination;
use App\Policies\PaymentStatusPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Milon\Barcode\Facades\DNS2DFacade;

class PaymentStatusController extends Controller
{

    /**
     * @var PaymentStatusPolicy
     */
    protected $policy;

    public function __construct(PaymentStatusPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function index(PaymentHistorySearchRequest $request, GetPaymentHistory $action)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        if (!$this->policy->viewAny($user)) {
            abort(403);
        }

        $sessionKey = 'payment_status_filters';

        $filterKeys = [
            'search_fields',
            'search_values',
            'page',
            'per_page',
            'order_by',
            'order_type',
            'date_from',
            'date_to',
            'status_filter'
        ];

        $hasFiltersInRequest = $request->anyFilled($filterKeys) || $request->hasAny($filterKeys);

        if (!$hasFiltersInRequest && $request->session()->has($sessionKey)) {
            $savedFilters = array_filter($request->session()->get($sessionKey));

            if (!empty($savedFilters)) {
                return redirect()->route($request->route()->getName(), $savedFilters);
            }
        }

        $filters = $request->validated();
        $data    = $action->setUser($user)->execute($filters);

        if (!empty(array_filter($filters))) {
            $request->session()->put($sessionKey, $filters);
        } else {
            $request->session()->forget($sessionKey);
        }

        return Inertia::render('PaymentStatus/Index', [
            'title'      => 'Transaction History',
            'additional' => [
                'data'     => PaymentHistoryResource::collection($data),
                'filters'  => $filters,
                'columns'  => $action->getColumns(),
                'urlIndex' => route('panel.payment-status.index'),
            ],
        ]);
    }


    public function show(Request $request, Manifest $manifest)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();
        if (!$this->policy->view($user, $manifest)) {
            abort(403);
        }

        $company = $manifest->company->load(
            "boat.fileable",
            "boat.boatman.fileable",
            "boat.boatmanMain",
            "boat.boatmanAsst"
        );

        return Inertia::render('PaymentStatus/Show', [
            "title" => "Detail Payment Status",
            "additional" => [
                "manifest" => (new ManifestFormResource($manifest->load("boatmanOther", "manifestFee", "guest.refNationality", "guest.activities")))->toArray($request),
                "urlBack" => route("panel.payment-status.index"),
                "urlDownload" => route("panel.payment-status.download", ["manifest" => $manifest->uuid]),
                "boats" => $company->boat ?? [],
                "company" => $company,
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
