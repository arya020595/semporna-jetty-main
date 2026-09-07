<?php

namespace App\Http\Controllers\Master;

use App\Actions\Destination\GetDestination;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTable\ActivityRequest;
use App\Http\Requests\ReferenceTable\DestinationSearchRequest;
use App\Http\Resources\RefTable\DestinationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\RefActivity;
use App\Models\RefDestination;
use App\Policies\ActivityPolicy;
use App\Policies\DestinationPolicy;

class DestinationController extends Controller
{
    protected $policy;

    public function __construct(
        DestinationPolicy $policy
    ) {
        $this->policy = $policy;
    }

    public function index(GetDestination $action, DestinationSearchRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->viewAny($userAuth)) {
            abort(403);
        }

        $filters = $request->validated();
        $data = $action->execute($filters);

        $request->session()->put('filters', $filters);

        return Inertia::render('Master/Destination/Index', [
            "title" => "Island Management - List",
            "additional" => [
                "data" => DestinationResource::collection($data),
                "filters" => $filters,
                "columns" => $action->getColumns(),
                "canCreate" => $this->policy->create($userAuth),
                "urlCreate" => route("panel.destination.create"),
                "urlIndex" => route("panel.destination.index")
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
        return Inertia::render('Master/Destination/Create', [
            "title" => "Island Management - Create",
            "additional" => [
                "filters" => $filters,
                "urlStore" => route("panel.destination.store"),
                "urlIndex" => route("panel.destination.index")
            ]
        ]);
    }

    public function store(ActivityRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->create($userAuth)) {
            abort(403);
        }

        $destination = DB::transaction(function () use ($request) {
            return RefDestination::create([
                "code" => $this->getCode(),
                "title" => $request->title,
                "type" => RefDestination::TYPE_DESTINATION
            ]);
        });

        $filters = $request->session()->get('filters');

        return redirect()->route("panel.destination.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Add Island $destination->title Success!"
            ]);
    }

    public function show(Request $request, RefDestination $destination)
    {

        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->view($userAuth, $destination)) {
            abort(403);
        }

        $filters = $request->session()->get('filters');
        return Inertia::render('Master/Destination/Show', [
            "title" => "Island Management - Show",
            "additional" => [
                "filters" => $filters,
                "data" => $destination,
                "canEdit" => $this->policy->update($userAuth, $destination),
                "urlEdit" => route("panel.destination.edit", $destination),
                "urlIndex" => route("panel.destination.index")
            ]
        ]);
    }

    public function edit(Request $request, RefDestination $destination)
    {

        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->update($userAuth, $destination)) {
            abort(403);
        }

        $filters = $request->session()->get('filters');
        return Inertia::render('Master/Destination/Edit', [
            "title" => "Island Management - Edit",
            "additional" => [
                "filters" => $filters,
                "data" => $destination,
                "canView" => $this->policy->view($userAuth, $destination),
                "urlShow" => route("panel.destination.show", $destination),
                "urlIndex" => route("panel.destination.index"),
                "urlUpdate" => route("panel.destination.update", $destination),
            ]
        ]);
    }

    public function update(RefDestination $destination, ActivityRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->update($userAuth, $destination)) {
            abort(403);
        }

        DB::transaction(function () use ($destination, $request) {
            $arrData = $request->validated();
            $destination->update($arrData);
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.destination.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Update Island $destination->title Success!"
            ]);
    }

    public function destroy(RefDestination $destination, Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->delete($userAuth, $destination)) {
            abort(403);
        }

        $itemTitle = $destination->title;
        DB::transaction(function () use ($destination) {
            $destination->update([
                "deleted_by" => Auth::id()
            ]);

            $destination->delete();
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.destination.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Delete Island $itemTitle Success!"
            ]);
    }

    public function getCode()
    {
        $maxId = RefDestination::max('id');
        return str_pad($maxId + 1, 5, "0", STR_PAD_LEFT);
    }
}
