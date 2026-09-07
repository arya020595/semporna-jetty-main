<?php

namespace App\Policies;

use App\Models\Approvement;
use App\Models\Manifest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserActivityPolicy
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
        return $user->can("user-activity-list");
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
        if ($user->hasRole(User::ROLE_OPERATOR_JETTY)) {
            return $user->can("user-activity-show") && $item->departure_id == $user->jetty_id;
        }

        if ($user->hasRole([User::ROLE_JABATAN_LAUT, User::ROLE_JABATAN_PELABUHAN, User::ROLE_PDRM, User::ROLE_SABAH_PARKS])) {
            return $user->can("user-activity-show") && $item->payment_status == Manifest::PAYMENT_STATUS_PAID;
        }

        return $user->can("user-activity-show");
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
        $alreadyApproved = false;
        $isStatusAllowed = in_array($item->status, [Manifest::STATUS_PENDING, Manifest::STATUS_APPROVED_PROGRESS]);
        if ($isStatusAllowed) {
            $role = $user->activeRole();
            $alreadyApproved = $item->approvement()
                ->where("version", $item->version)
                ->where("role_id", $role->id)
                ->where("status", Approvement::STATUS_APPROVED)
                ->exists();
        }

        return $user->can("user-activity-approve")
            && $item->payment_status == Manifest::PAYMENT_STATUS_PAID
            && $isStatusAllowed
            && !$alreadyApproved;
    }


    public function jettyApproval(User $user, Manifest $item)
    {
        return $user->hasRole(User::ROLE_OPERATOR_JETTY)
            && $item->status == Manifest::STATUS_APPROVED
            && !in_array($item->jetty_approval_status, [Manifest::STATUS_APPROVED, Manifest::STATUS_REJECTED, Manifest::STATUS_AMEND]);
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

        // Prevent deleting if manifest has ever been linked to a payment (pending or paid) -> preventing race condition
        $hasPaymentLinked = $item->manifestFee()
            ->whereNotNull('payment_id')
            ->exists();

        if ($hasPaymentLinked) {
            return false;
        }

        if ($user->hasRole(User::ROLE_OPERATOR_JETTY)) {
            return $user->can("user-activity-delete") && $item->departure_id == $user->jetty_id;
        }

        return $user->can("user-activity-delete");
    }
}
