<?php

namespace App\Http\Controllers\UserManagement;

use App\Actions\User\GetJettyDatatables;
use App\Actions\User\GetUsersDatatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCredentialsRequest;
use App\Http\Requests\UserJettyRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserSearchRequest;
use App\Http\Resources\UserJettyResource;
use App\Http\Resources\UserJettyTableResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VueSelect\RoleSelectResource;
use App\Models\User;
use App\Actions\User\GenerateStaffId;
use App\Policies\UserJettyPolicy;
use App\Policies\UserPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserJettyController extends Controller
{

    protected $policy;

    public function __construct(UserJettyPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function index(GetJettyDatatables $action, UserSearchRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();
        if (!$this->policy->viewAny($authUser)) {
            abort(403);
        }

        $filters = $request->validated();
        $users = $action->execute($filters);

        $request->session()->put('filters', $filters);

        return Inertia::render('UserManagement/UserJetty/Index', [
            "title" => "User Management - List",
            "additional" => [
                "users" => UserJettyTableResource::collection($users),
                "filters" => $filters,
                "columns" => $action->getColumns(),
                "canCreate" => $this->policy->create($authUser),
                "urlCreate" => route("panel.user-jetty.create"),
                "urlIndex" => route("panel.user-jetty.index")
            ]
        ]);
    }

    public function create(Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();
        if (!$this->policy->create($authUser)) {
            abort(403);
        }

        $filters = $request->session()->get('filters');
        return Inertia::render('UserManagement/UserJetty/Create', [
            "title" => "User Management - List",
            "additional" => [
                "filters" => $filters,
                "urlStore" => route("panel.user-jetty.store"),
                "urlIndex" => route("panel.user-jetty.index")
            ]
        ]);
    }

    public function store(UserJettyRequest $request, GenerateStaffId $generateStaffId)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();
        if (!$this->policy->create($authUser)) {
            abort(403);
        }

        DB::transaction(function () use ($request, $authUser, $generateStaffId) {
            $arrUser = $request->validated();
            $arrUser["password"] = Hash::make($arrUser["password"]);
            $arrUser["created_by"] = $authUser->id;
            $arrUser["jetty_id"] = $authUser->jetty_id;
            $arrUser["staf_id"] = $generateStaffId->execute();

            $user = User::create($arrUser);

            $user->save();

            $user->roles()->sync([User::ROLE_OPERATOR_JETTY]);
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.user-jetty.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Update User Success!"
            ]);
    }

    public function show(Request $request, User $user)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();
        if (!$this->policy->view($authUser, $user)) {
            abort(403);
        }

        $filters = $request->session()->get('filters');
        return Inertia::render('UserManagement/UserJetty/Show', [
            "title" => "User Management - Show",
            "additional" => [
                "user" => (new UserJettyResource($user))->toArray($request),
                "filters" => $filters,
                "canEdit" => $this->policy->update($authUser, $user),
                "urlEdit" => route("panel.user-jetty.edit", $user),
                "urlIndex" => route("panel.user-jetty.index")
            ]
        ]);
    }

    public function edit(Request $request, User $user)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();
        if (!$this->policy->update($authUser, $user)) {
            abort(403);
        }

        $roles = Role::all();

        $filters = $request->session()->get('filters');
        return Inertia::render('UserManagement/UserJetty/Edit', [
            "title" => "User Management - Edit",
            "additional" => [
                "user" => (new UserResource($user->load("roles")))->toArray($request),
                "filters" => $filters,
                "roles" => RoleSelectResource::collection($roles),
                "canView" => $this->policy->view($authUser, $user),

                "urlShow" => route("panel.user-jetty.show", $user),
                "urlUpdate" => route("panel.user-jetty.update", $user),
                "urlIndex" => route("panel.user-jetty.index"),
                "urlUpdateCreds" => route("panel.user-jetty.update-credentials", $user)
            ]
        ]);
    }

    public function update(UserJettyRequest $request, User $user)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();
        if (!$this->policy->update($authUser, $user)) {
            abort(403);
        }

        DB::transaction(function () use ($request, $authUser, $user) {
            $arrUser = $request->validated();

            $arrUser["updated_by"] = Auth::id();
            $arrUser["jetty_id"] = $authUser->jetty_id;

            $user->update($arrUser);
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.user-jetty.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Update User Success!"
            ]);
    }

    public function updateCredentials(UserCredentialsRequest $request, User $user)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();
        if (!$this->policy->update($authUser, $user)) {
            abort(403);
        }

        $arrUser = $request->validated();

        DB::transaction(function () use ($arrUser, $user) {
            $user->email = $arrUser["email"];
            if ($arrUser["password"]) {
                $user->password = Hash::make($arrUser["password"]);
            }
            $user->updated_by = Auth::id();
            $user->save();
        });

        return redirect()->back()
            ->with("message", [
                "status" => "success",
                "message" => "Update User Credentials Success!"
            ]);
    }

    public function destroy(User $user)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();
        if (!$this->policy->delete($authUser, $user)) {
            abort(403);
        }

        $name = $user->name;
        DB::transaction(function () use ($user) {
            $user->deleted_by = Auth::id();
            $user->email = $user->email . '_deleted_' . time();
            $user->save();
            $user->delete();
        });

        return redirect()->back()
            ->with("message", [
                "status" => "success",
                "message" => "Delete User '$name' Success!"
            ]);
    }
}
