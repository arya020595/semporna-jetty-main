<?php

namespace App\Policies\Authorities;

use App\Models\Approvement;
use App\Models\Manifest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MyDashboardPolicy
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
        return $user->can("autho-my-dashboard-list");
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
        if ($user->hasRole([
            User::ROLE_JABATAN_LAUT,
            User::ROLE_JABATAN_PELABUHAN,
            User::ROLE_PDRM,
            User::ROLE_SABAH_PARKS
        ])) {
            if ($user->jetty_id && $item->departure_id != $user->jetty_id) {
                return false;
            }

            return $user->can("autho-my-dashboard-show") && $item->payment_status == Manifest::PAYMENT_STATUS_PAID;
        }

        return $user->can("autho-my-dashboard-show");
    }


    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Manifest  $item
     * @return bool
     */
    public function approve(User $user, Manifest $item): bool
    {
        if ($user->hasRole([
            User::ROLE_JABATAN_LAUT,
            User::ROLE_JABATAN_PELABUHAN,
            User::ROLE_PDRM,
            User::ROLE_SABAH_PARKS
        ])) {
            if ($user->jetty_id && $item->departure_id != $user->jetty_id) {
                return false;
            }
        }

        $alreadyApproved = false;
        $isStatusAllowed = in_array($item->status, [
            Manifest::STATUS_PENDING,
            Manifest::STATUS_APPROVED_PROGRESS,
            Manifest::STATUS_AMEND
        ]);

        if ($isStatusAllowed) {
            $role = $user->activeRole();
            $alreadyApproved = $item->approvement()
                ->where("version", $item->version)
                ->where("role_id", $role->id)
                ->where("status", Approvement::STATUS_APPROVED)
                ->exists();
        }

        return $user->can("autho-my-dashboard-approve")
            && $item->payment_status == Manifest::PAYMENT_STATUS_PAID
            && $isStatusAllowed
            && !$alreadyApproved;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function delete(User $user, Manifest $item): bool
    {
        if ($item->status == Manifest::STATUS_APPROVED) {
            return false;
        }

        return $user->can("autho-my-dashboard-delete");
    }
}
