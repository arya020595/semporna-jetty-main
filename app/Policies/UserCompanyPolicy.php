<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class UserCompanyPolicy
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
        return $user->can("user-company-list");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return $user->can("user-company-show")
            && $model->company_id == $user->company_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can("user-company-create");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $user->can("user-company-edit")
            && $model->company_id == $user->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        if ($model->hasRole(User::ROLE_AGENT)) {
            return false;
        }

        return $user->can("user-company-delete")
            && $model->company_id == $user->company_id;
    }
}
