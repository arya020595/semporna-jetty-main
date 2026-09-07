<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\RefNationality;

class NationalityPolicy
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
        return $user->can("nationality-list");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function view(User $user, RefNationality $nationality): bool
    {
        return $user->can("nationality-show");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can("nationality-create");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function update(User $user, RefNationality $nationality): bool
    {
        return $user->can("nationality-edit");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function delete(User $user, RefNationality $nationality): bool
    {
        return $user->can("nationality-delete");
    }
}
