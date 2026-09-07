<?php

namespace App\Policies;

use App\Models\Approvement;
use App\Models\Manifest;
use App\Models\RefDestination;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class ManifestPolicy
{
    use HandlesAuthorization;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
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
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can("manifest-create");
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
        if ($user->hasRole([User::ROLE_AGENT, User::ROLE_AGENT_EMPLOYEE])) {
            return $user->can("manifest-show") && $item->company_id == $user->company_id;
        }

        // if ($user->hasRole(User::ROLE_OPERATOR_JETTY)) {
        //     return true;
        // }

        return $user->can("manifest-show");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function update(User $user, Manifest $item): bool
    {
        // Superadmin can always edit (for operational / support purposes)
        if ($user->hasRole(User::ROLE_SUPERADMIN)) {
            return $user->can("manifest-edit");
        }

        // Only users with role Agent or Agent Employee can edit manifests
        if (!$user->hasRole([
            User::ROLE_AGENT,
            User::ROLE_AGENT_EMPLOYEE
        ])) {
            return false;
        }

        // Company must own the manifest
        if ($item->company_id !== $user->company_id) {
            return false;
        }

        // Company must have the permission
        if (!$user->can("manifest-edit")) {
            return false;
        }

        $bypassApproval = config('features.bypass_approval_flow');

        if ($bypassApproval) {
            // Bypass mode: editable if PENDING/AMEND, or APPROVED + jetty not yet approved/amending
            return (
                $item->status == Manifest::STATUS_PENDING
                || $item->status == Manifest::STATUS_AMEND
            ) || (
                $item->status == Manifest::STATUS_APPROVED
                && in_array($item->jetty_approval_status, [
                    Manifest::STATUS_PENDING,
                    Manifest::STATUS_AMEND,
                ])
            );
        }

        // Exception for Seafest Jetty with bypass payment enabled
        $isSeafest = $item->departure && $item->departure->code == RefDestination::CODE_SEAFEST;
        if (config('features.bypass_seafest_payment') && $isSeafest) {
            return true;
        }

        // Non-bypass (Authority Approval Flow active):
        // - PENDING  (0) = no authority has acted yet → EDITABLE
        // - AMEND    (3) = at least one authority requested amendment → EDITABLE
        // - APPROVED_PROGRESS (2) = at least one authority approved → LOCKED
        // - APPROVED (1) = all approved → LOCKED (unless jetty requests amendment)
        return (
            $item->status == Manifest::STATUS_PENDING
            || $item->status == Manifest::STATUS_AMEND
            || (
                $item->status == Manifest::STATUS_APPROVED
                && $item->jetty_approval_status == Manifest::STATUS_AMEND
            )
        );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Manifest  $item
     * @return bool
     */
    public function delete(User $user, Manifest $item): bool
    {
        // Cannot delete non-drafted, paid, and approved manifests
        if (
            $item->is_final ||
            $item->payment_status == Manifest::PAYMENT_STATUS_PAID ||
            $item->status == Manifest::STATUS_APPROVED
        ) {
            return false;
        }

        // Only can delete if Tour Operators with their respective manifest and company id
        if ($user->hasRole([User::ROLE_AGENT, User::ROLE_AGENT_EMPLOYEE])) {
            return $user->can("user-activity-delete") && $item->company_id == $user->company_id;
        }

        return $user->can("user-activity-delete");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     */
    public function approval(User $user, Manifest $item): bool
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
            && !in_array($item->jetty_approval_status, [Manifest::STATUS_APPROVED, Manifest::STATUS_REJECTED]);
    }
}
