<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class UserApprovalPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can("user-approval-list");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return bool
     */
    public function approve(User $user, User $model): bool
    {
        if ($user->hasRole(User::ROLE_OPERATOR_JETTY)) {
            return $user->can("user-approval-approve") && ($user->jetty_id == $model->jetty_id);
        }

        return $user->can("user-approval-approve");
    }
}
