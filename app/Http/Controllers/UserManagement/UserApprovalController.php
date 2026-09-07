<?php

namespace App\Http\Controllers\UserManagement;

use App\Actions\User\GetUsersApprovalDatatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserSearchRequest;
use App\Http\Resources\UserApprovalTableResource;
use App\Models\User;
use App\Policies\UserApprovalPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserApprovalController extends Controller
{

    protected $policy;

    public function __construct(UserApprovalPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function index(GetUsersApprovalDatatables $action, UserSearchRequest $request)
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

        return Inertia::render('UserManagement/UserApproval/Index', [
            "title" => "User Approval - List",
            "additional" => [
                "users" => UserApprovalTableResource::collection($users),
                "filters" => $filters,
                "columns" => $action->getColumns(),
                "urlIndex" => route("panel.user-approval.index")
            ]
        ]);
    }

    public function approve(Request $request, User $user)
    {
        /**
         * @var \App\Models\User
         */
        $authUser = Auth::user();

        if (!$this->policy->viewAny($authUser)) {
            abort(403);
        }

        DB::transaction(function () use ($request, $user) {
            $user->status = $request->is_approved
                ? User::STATUS_ACTIVE
                : User::STATUS_NONACTIVE;
            $user->save();
        });

        $filters = $request->session()->get('filters');
        return redirect()->route("panel.user-approval.index", $filters)
            ->with("message", [
                "status" => "success",
                "message" => "User Approved!"
            ]);
    }
}
