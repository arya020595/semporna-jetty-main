<?php

namespace App\Http\Controllers\UserManagement;

use App\Actions\User\GetUsersDatatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCredentialsRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserSearchRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTableResource;
use App\Http\Resources\VueSelect\RoleSelectResource;
use App\Models\RefDestination;
use App\Models\User;
use App\Actions\User\GenerateStaffId;
use App\Policies\UserPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    protected $userPolicy;

    public function __construct(UserPolicy $userPolicy)
    {
        $this->userPolicy = $userPolicy;
    }

    public function index(GetUsersDatatables $getUsers, UserSearchRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();

        $this->authorize('viewAny', User::class);

        $filters = $request->validated();
        $users = $getUsers->execute($filters);

        $request->session()->put('filters', $filters);

        return Inertia::render('UserManagement/User/Index', [
            "title" => "User Management - List",
            "additional" => [
                "users" => UserTableResource::collection($users),
                "filters" => $filters,
                "columns" => $getUsers->getColumns(),
                "canCreate" => $this->userPolicy->create($authUser),
                "urlCreate" => route("panel.user.create"),
                "urlIndex" => route("panel.user.index")
            ]
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', User::class);

        $roles = Role::all();

        $filters = $request->session()->get('filters');

        $arrJetty = RefDestination::query()
            ->where("type", RefDestination::TYPE_DEPARTURE)
            ->get();

        return Inertia::render('UserManagement/User/Create', [
            "title" => "User Management - List",
            "additional" => [
                "filters" => $filters,
                "roles" => RoleSelectResource::collection($roles),
                "arrJetty" => $arrJetty->map(function ($item) {
                    return [
                        "id" => $item->id,
                        "description" => $item->title
                    ];
                }),
                "urlStore" => route("panel.user.store"),
                "urlIndex" => route("panel.user.index"),
                "urlCompany" => route("resources.company.index")
            ]
        ]);
    }

    public function store(UserRequest $request, GenerateStaffId $generateStaffId)
    {
        $this->authorize('create', User::class);

        DB::transaction(function () use ($request, $generateStaffId) {
            $arrUser = $request->validated();
            $arrUser["password"] = Hash::make($arrUser["password"]);
            $arrUser["created_by"] = Auth::id();

            $authorityRoles = [
                User::ROLE_PDRM,
                User::ROLE_JABATAN_LAUT,
                User::ROLE_SABAH_PARKS,
                User::ROLE_JABATAN_PELABUHAN
            ];
            $arrUser["jetty_id"] = ($arrUser['role'] == User::ROLE_OPERATOR_JETTY || in_array($arrUser['role'], $authorityRoles))
                ? ($arrUser["jetty_id"] ?? null)
                : null;

            $arrUser["company_id"] = $arrUser['role'] == User::ROLE_AGENT
                ? $arrUser["company_id"]
                : null;

            $arrUser["staf_id"] = $generateStaffId->execute();

            $user = User::create($arrUser);

            $user->roles()->sync($arrUser['role']);
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.user.index", $filters)
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

        $this->authorize('view', $user);

        $filters = $request->session()->get('filters');
        return Inertia::render('UserManagement/User/Show', [
            "title" => "User Management - Show",
            "additional" => [
                "user" => (new UserResource($user->load("roles")))->toArray($request),
                "accessLogs" => $user->accessLog()
                    ->orderBy("created_at", "DESC")
                    ->limit(10)
                    ->get(),
                "filters" => $filters,
                "canEdit" => $this->userPolicy->update($authUser, $user),
                "urlEdit" => route("panel.user.edit", $user),
                "urlIndex" => route("panel.user.index")
            ]
        ]);
    }

    public function edit(Request $request, User $user)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();

        $this->authorize('update', $user);

        $roles = Role::all();

        $filters = $request->session()->get('filters');

        $arrJetty = RefDestination::query()
            ->where("type", RefDestination::TYPE_DEPARTURE)
            ->get();

        return Inertia::render('UserManagement/User/Edit', [
            "title" => "User Management - Edit",
            "additional" => [
                "user" => (new UserResource($user->load("roles")))->toArray($request),
                "filters" => $filters,
                "roles" => RoleSelectResource::collection($roles),
                "arrJetty" => $arrJetty->map(function ($item) {
                    return [
                        "id" => $item->id,
                        "description" => $item->title
                    ];
                }),
                "canView" => $this->userPolicy->view($authUser, $user),

                "urlShow" => route("panel.user.show", $user),
                "urlUpdate" => route("panel.user.update", $user),
                "urlIndex" => route("panel.user.index"),
                "urlCompany" => route("resources.company.index"),
                "urlUpdateCreds" => route("panel.user.update-credentials", $user)
            ]
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        DB::transaction(function () use ($request, $user) {
            $arrUser = $request->validated();

            $arrUser["updated_by"] = Auth::id();

            $authorityRoles = [
                User::ROLE_PDRM,
                User::ROLE_JABATAN_LAUT,
                User::ROLE_SABAH_PARKS,
                User::ROLE_JABATAN_PELABUHAN
            ];
            $arrUser["jetty_id"] = ($arrUser['role'] == User::ROLE_OPERATOR_JETTY || in_array($arrUser['role'], $authorityRoles))
                ? ($arrUser["jetty_id"] ?? null)
                : null;

            $arrUser["company_id"] = $arrUser['role'] == User::ROLE_AGENT
                ? $arrUser["company_id"]
                : null;

            $user->update($arrUser);
            $user->roles()->sync($arrUser['role']);
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.user.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "Update User Success!"
            ]);
    }

    public function updateCredentials(UserCredentialsRequest $request, User $user)
    {
        $this->authorize('update', $user);
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
        $this->authorize('delete', $user);

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
