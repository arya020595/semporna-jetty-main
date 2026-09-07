<?php

namespace App\Http\Controllers;

use App\Actions\Report\GetReportManifest;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Http\Requests\SupportRequest;
use App\Models\Company;
use App\Models\Manifest;
use App\Models\RefDestination;
use App\Models\Support;
use App\Models\User;
use App\Policies\ReportPolicy;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    protected ReportPolicy $policy;

    public function __construct(ReportPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function index(ReportRequest $request)
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        if (!$this->policy->viewAny($user)) {
            abort(403);
        }

        $startDate = Manifest::min("created_at");
        $startYear = $startDate ? substr($startDate, 0, 4) : date("Y");
        $endYear = date("Y");

        $arrYear = [];
        for ($i = $startYear; $i <= $endYear; $i++) {
            $arrYear[] = $i;
        }

        $arrRequest = $request->validated();
        $isOperator = $user->hasRole(User::ROLE_OPERATOR_JETTY);

        // For jetty operators, always force departure_id to their own jetty to prevent viewing others
        if ($isOperator) {
            $arrRequest['departure_id'] = $user->jetty_id;
        }

        return Inertia::render('Report/Index', [
            "title" => "Report",
            "additional" => [
                "formData" => $arrRequest,
                "isOperator" => $isOperator,
                "urlIndex" => route("panel.report.index"),
                "urlDownload" => route("panel.report.download", $arrRequest),
                "urlDeparture" => route("resources.departure.index")
            ]
        ]);
    }

    public function download(ReportRequest $request, GetReportManifest $action)
    {
        /** @var User $user */
        $user = Auth::user();

        $arrRequest = $request->validated();
        $isOperator = $user->hasRole(User::ROLE_OPERATOR_JETTY);

        // Security check: If operator, force departure_id to their own jetty
        $departureId = $isOperator ? $user->jetty_id : ($arrRequest["departure_id"] ?? false);
        $start = $arrRequest["start"] ?? Carbon::now()->subYear()->format("Y-m-d");
        $end = $arrRequest["end"] ?? Carbon::now()->format("Y-m-d");

        $filters = [
            "departure_id" => $departureId,
            "start" => $start,
            "end" => $end
        ];

        $data = $action->execute($filters);

        $departure = RefDestination::find($departureId);

        $totalManifest = $data->sum("total_manifest");
        $totalTourOperators = $departureId
            ? Manifest::where("departure_id", $departureId)
                ->where("is_final", 1)
                ->whereBetween("departure_date", [$start, $end])
                ->distinct("company_id")
                ->count("company_id")
            : $data->sum("total_unique_companies");

        $pdf = Pdf::loadView("download-pdf.report", [
            "title" => "Report from $start to $end",
            "departure" => optional($departure)->title,
            "start" => $start,
            "end" => $end,
            "data" => $data,
            "totalManifest" => $totalManifest,
            "totalTourOperators" => $totalTourOperators,
        ])
            ->setPaper('A4', 'landscape')
            ->setOption(['dpi' => 110]);

        return $pdf->stream();
    }
}
