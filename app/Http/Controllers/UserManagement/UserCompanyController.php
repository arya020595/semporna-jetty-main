<?php

namespace App\Http\Controllers\UserManagement;

use App\Actions\User\GetCompanyDatatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCredentialsRequest;
use App\Http\Requests\UserJettyRequest;
use App\Http\Requests\UserSearchRequest;
use App\Http\Resources\UserCompanyTableResource;
use App\Http\Resources\VueSelect\RoleSelectResource;
use App\Http\Resources\UserJettyResource;
use App\Models\User;
use App\Actions\User\GenerateStaffId;
use App\Http\Resources\UserResource;
use App\Policies\UserCompanyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserCompanyController extends Controller
{

    protected $policy;

    public function __construct(UserCompanyPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function index(GetCompanyDatatables $action, UserSearchRequest $request)
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

        return Inertia::render('UserManagement/UserCompany/Index', [
            "title" => "User Management - List",
            "additional" => [
                "users" => UserCompanyTableResource::collection($users),
                "filters" => $filters,
                "columns" => $action->getColumns(),
                "canCreate" => $this->policy->create($authUser),
                "urlCreate" => route("panel.user-company.create"),
                "urlIndex" => route("panel.user-company.index")
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
        return Inertia::render('UserManagement/UserCompany/Create', [
            "title" => "User Management - List",
            "additional" => [
                "filters" => $filters,
                "urlStore" => route("panel.user-company.store"),
                "urlIndex" => route("panel.user-company.index")
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
            $arrUser["company_id"] = $authUser->company_id;
            $arrUser["staf_id"] = $generateStaffId->execute();

            $user = User::create($arrUser);

            $user->save();

            $user->roles()->sync([User::ROLE_AGENT_EMPLOYEE]);
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.user-company.index", $filters)
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
        return Inertia::render('UserManagement/UserCompany/Show', [
            "title" => "User Management - Show",
            "additional" => [
                "user" => (new UserJettyResource($user))->toArray($request),
                "filters" => $filters,
                "canEdit" => $this->policy->update($authUser, $user),
                "urlEdit" => route("panel.user-company.edit", $user),
                "urlIndex" => route("panel.user-company.index")
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
        return Inertia::render('UserManagement/UserCompany/Edit', [
            "title" => "User Management - Edit",
            "additional" => [
                "user" => (new UserResource($user->load("roles")))->toArray($request),
                "filters" => $filters,
                "roles" => RoleSelectResource::collection($roles),
                "canView" => $this->policy->view($authUser, $user),

                "urlShow" => route("panel.user-company.show", $user),
                "urlUpdate" => route("panel.user-company.update", $user),
                "urlIndex" => route("panel.user-company.index"),
                "urlUpdateCreds" => route("panel.user-company.update-credentials", $user)
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
            $arrUser["company_id"] = $authUser->company_id;

            $user->update($arrUser);
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.user-company.index", $filters)
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
