<?php

namespace App\Policies;

use App\Models\Manifest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class PaymentStatusPolicy
{
    use HandlesAuthorization;

    /**
     * @var Collection
     */
    protected $roles;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->roles = Role::all();
    }



    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can("payment-status-list");
    }


    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function view(User $user, Manifest $item): bool
    {
        if ($user->hasRole($this->roles->find(User::ROLE_OPERATOR_JETTY)->name)) {
            return $user->can("payment-status-show") && $item->departure_id == $user->jetty_id;
        }
        return $user->can("payment-status-show");
    }
}
