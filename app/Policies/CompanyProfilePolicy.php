<?php

namespace App\Policies;

use App\Models\RefActivity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyProfilePolicy
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
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function view(User $user): bool
    {
        if (!$user->company) {
            return false;
        }

        return $user->can("company-profile-show");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        if (!$user->company) {
            return false;
        }

        return $user->can("company-profile-create");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function update(User $user): bool
    {
        if (!$user->company) {
            return false;
        }

        return $user->can("company-profile-edit");
    }
}
