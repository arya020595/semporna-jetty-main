<?php

namespace App\Http\Controllers\Api;

use App\Actions\API\GetManifestActivity;
use App\Actions\CompanyManifest\ApprovementManifest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ActivitySearchRequest;
use App\Http\Requests\CompanyManifest\ApprovementRequest;
use App\Http\Resources\API\ManifestResource;
use App\Http\Resources\API\UserActivityDetailResource;
use App\Jobs\JobCalculateManifestSummary;
use App\Models\Manifest;
use App\Models\User;
use App\Policies\Authorities\MyDashboardPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ActivityController extends Controller
{

    protected MyDashboardPolicy $policy;

    public function __construct(MyDashboardPolicy $policy)
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

        $filters = $request->validated();
        $data = $action->setUser($userAuth)->execute($filters);
        $dateRange = $action->getDateRange($filters);

        return response()->json([
            "message" => "List Manifest Activity",
            "data" => [
                "list" => ManifestResource::collection($data),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'min_date' => $dateRange->min_date,
                    'max_date' => $dateRange->max_date,
                ],
                "filters" => $filters
            ]
        ], 200);
    }

    public function show(Request $request, $manifest)
    {
        $manifest = Manifest::findOrFail($manifest);

        /**
         * @var User
         */
        $userAuth = Auth::user();
        if (!$this->policy->view($userAuth, $manifest)) {
            abort(403);
        }

        $manifest = $manifest->load(
            "boatmanOther",
            "manifestFee",
            "guest.refNationality",
            "guest.activities",
            "latestApprovementByRole.user",
            "latestApprovementByRole.role",
        );

        return response()->json([
            "message" => "Show Detail Manifest",
            "data" => [
                "manifest" => (new UserActivityDetailResource($manifest))->toArray($request),
                "canApprove" => $this->policy->approve($userAuth, $manifest)
            ]
        ], 200);
    }

    public function approval(ApprovementRequest $request, $manifest, ApprovementManifest $action)
    {
        $manifest = Manifest::findOrFail($manifest);
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
            $approvement =  $action->execute($manifest, $userAuth, $arrRequest);

            if ($manifest->status == Manifest::STATUS_APPROVED) {
                JobCalculateManifestSummary::dispatch($manifest->id);
            }

            return $approvement;
        });

        return response()->json([
            "message" => "Manifest " . $approvement->status_text . "!",
            "data" => [
                "approvement" => $approvement
            ]
        ], 200);
    }

    public function scan(Request $request)
    {
        $arrValidated = Request::validate([
            "code" => "required"
        ]);

        $manifest = Manifest::query()
            ->where("form_number", $arrValidated["code"] ?? false)
            ->where("payment_status", Manifest::PAYMENT_STATUS_PAID)
            ->select("id", "form_number")
            ->first();

        if (!$manifest) {
            return response()->json([
                "message" => "Manifest Not Found!",
                "manifest" => null,
                "status" => false
            ]);
        }

        return response()->json([
            "message" => "Manifest Found!",
            "manifest" => $manifest,
            "status" => true
        ]);
    }
}
