<?php

namespace App\Http\Controllers\Master;

use App\Actions\Activity\GetActivity;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceTable\ActivityRequest;
use App\Http\Requests\ReferenceTable\ActivitySearchRequest;
use App\Http\Resources\RefTable\ActivityResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\RefActivity;
use App\Policies\ActivityPolicy;

class ActivityController extends Controller
{
    protected $policy;

    public function __construct(
        ActivityPolicy $policy
    ) {
        $this->policy = $policy;
    }

    public function index(GetActivity $action, ActivitySearchRequest $request)
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

        return Inertia::render('Master/Activity/Index', [
            "title" => "Activity Management - List",
            "additional" => [
                "data" => ActivityResource::collection($data),
                "filters" => $filters,
                "columns" => $action->getColumns(),
                "canCreate" => $this->policy->create($userAuth),
                "urlCreate" => route("panel.activity.create"),
                "urlIndex" => route("panel.activity.index")
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
        return Inertia::render('Master/Activity/Create', [
            "title" => "Activity Management - Create",
            "additional" => [
                "filters" => $filters,
                "urlStore" => route("panel.activity.store"),
                "urlIndex" => route("panel.activity.index")
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

        $activity = DB::transaction(function () use ($request) {
            return RefActivity::create([
                "code" => $this->getCode(),
                "title" => $request->title
            ]);
        });

        $filters = $request->session()->get('filters');

        return redirect()->route("panel.activity.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Add Activity $activity->title Success!"
            ]);
    }

    public function show(Request $request, RefActivity $activity)
    {

        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->view($userAuth, $activity)) {
            abort(403);
        }

        $filters = $request->session()->get('filters');
        return Inertia::render('Master/Activity/Show', [
            "title" => "Activity Management - Show",
            "additional" => [
                "filters" => $filters,
                "data" => $activity,
                "canEdit" => $this->policy->update($userAuth, $activity),
                "urlEdit" => route("panel.activity.edit", $activity),
                "urlIndex" => route("panel.activity.index")
            ]
        ]);
    }

    public function edit(Request $request, RefActivity $activity)
    {

        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->update($userAuth, $activity)) {
            abort(403);
        }

        $filters = $request->session()->get('filters');
        return Inertia::render('Master/Activity/Edit', [
            "title" => "Activity Management - Edit",
            "additional" => [
                "filters" => $filters,
                "data" => $activity,
                "canView" => $this->policy->view($userAuth, $activity),
                "urlShow" => route("panel.activity.show", $activity),
                "urlIndex" => route("panel.activity.index"),
                "urlUpdate" => route("panel.activity.update", $activity),
            ]
        ]);
    }

    public function update(RefActivity $activity, ActivityRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->update($userAuth, $activity)) {
            abort(403);
        }

        DB::transaction(function () use ($activity, $request) {
            $arrData = $request->validated();
            $activity->update($arrData);
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.activity.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Update Activity $activity->title Success!"
            ]);
    }

    public function destroy(RefActivity $activity, Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $userAuth = Auth::user();
        if (!$this->policy->delete($userAuth, $activity)) {
            abort(403);
        }

        $activityTitle = $activity->title;
        DB::transaction(function () use ($activity) {
            $activity->update([
                "deleted_by" => Auth::id()
            ]);

            $activity->delete();
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.activity.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Delete Activity $activityTitle Success!"
            ]);
    }

    public function getCode()
    {
        $maxId = RefActivity::max('id');
        return str_pad($maxId + 1, 5, "0", STR_PAD_LEFT);
    }
}
