<?php

namespace App\Http\Controllers\Master;

use App\Actions\Nationality\GetNationality;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTable\NationalityRequest;
use App\Http\Requests\ReferenceTable\NationalitySearchRequest;
use App\Http\Resources\RefTable\NationalityResource;
use App\Policies\NationalityPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\RefNationality;

class NationalityController extends Controller
{
    protected $policy;

    public function __construct(
        NationalityPolicy $policy
    ) {
        $this->policy = $policy;
    }

    public function index(GetNationality $getNationality, NationalitySearchRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->viewAny($userAuth)) {
            abort(403);
        }

        $filters = $request->validated();
        $nationality = $getNationality->execute($filters);

        $request->session()->put('filters', $filters);

        return Inertia::render('Master/Nationality/Index', [
            "title" => "Nationality Management - List",
            "additional" => [
                "nationality" => NationalityResource::collection($nationality),
                "filters" => $filters,
                "columns" => $getNationality->getColumns(),
                "canCreate" => $this->policy->create($userAuth),
                "urlCreate" => route("panel.nationality.create"),
                "urlIndex" => route("panel.nationality.index")
            ]
        ]);
    }

    public function create(Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->create($userAuth)) {
            abort(403);
        }
        $filters = $request->session()->get('filters');
        return Inertia::render('Master/Nationality/Create', [
            "title" => "Nationality Management - Create",
            "additional" => [
                "filters" => $filters,
                "urlStore" => route("panel.nationality.store"),
                "urlIndex" => route("panel.nationality.index")
            ]
        ]);
    }

    public function store(NationalityRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->create($userAuth)) {
            abort(403);
        }
        $nationality = DB::transaction(function () use ($request) {
            return RefNationality::create([
                "code" => $this->getCode(),
                "title" => $request->title
            ]);
        });

        $filters = $request->session()->get('filters');

        return redirect()->route("panel.nationality.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Add Nationality $nationality->title Success!"
            ]);
    }

    public function show(Request $request, RefNationality $nationality)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->view($userAuth, $nationality)) {
            abort(403);
        }

        $filters = $request->session()->get('filters');
        return Inertia::render('Master/Nationality/Show', [
            "title" => "Nationality Management - Show",
            "additional" => [
                "filters" => $filters,
                "nationality" => $nationality,
                "canEdit" => $this->policy->update($userAuth, $nationality),
                "urlEdit" => route("panel.nationality.edit", $nationality),
                "urlIndex" => route("panel.nationality.index")
            ]
        ]);
    }

    public function edit(Request $request, RefNationality $nationality)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->update($userAuth, $nationality)) {
            abort(403);
        }

        $filters = $request->session()->get('filters');
        return Inertia::render('Master/Nationality/Edit', [
            "title" => "Nationality Management - Edit",
            "additional" => [
                "filters" => $filters,
                "nationality" => $nationality,
                "canView" => $this->policy->view($userAuth, $nationality),
                "urlShow" => route("panel.nationality.show", $nationality),
                "urlIndex" => route("panel.nationality.index"),
                "urlUpdate" => route("panel.nationality.update", $nationality),
            ]
        ]);
    }

    public function update(RefNationality $nationality, NationalityRequest $request)
    {

        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->update($userAuth, $nationality)) {
            abort(403);
        }

        DB::transaction(function () use ($nationality, $request) {
            $arrData = $request->validated();
            $nationality->update($arrData);
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.nationality.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Update Nationality $nationality->title Success!"
            ]);
    }

    public function destroy(RefNationality $nationality, Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->delete($userAuth, $nationality)) {
            abort(403);
        }

        $nationalityTitle = $nationality->title;
        DB::transaction(function () use ($nationality) {
            $nationality->delete();
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.nationality.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Delete Nationality $nationalityTitle Success!"
            ]);
    }

    public function getCode()
    {
        $maxCode = RefNationality::max('code');
        $toNumber = (int) $maxCode;
        if ($toNumber > 0) {
            $toNumber = $toNumber + 1;
        } else {
            $toNumber = 1;
        }
        return sprintf("%05s", $toNumber);
    }
}
