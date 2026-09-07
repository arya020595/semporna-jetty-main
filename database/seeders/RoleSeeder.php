<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{

    protected $roles = [
        [
            "name" => "Super Admin",
            "default_route" => "panel.dashboard",
            "permissions" => [
                'dashboard-open',

                'reporting-list',
                'reporting-download',

                'user-management-open',

                'user-account-list',
                'user-account-create',
                'user-account-show',
                'user-account-edit',
                'user-account-delete',
                'user-account-approval',

                'role-list',
                'role-create',
                'role-show',
                'role-edit',
                'role-delete',

                'user-approval-list',
                'user-approval-approve',

                'master-open',

                'destination-list',
                'destination-create',
                'destination-show',
                'destination-edit',
                'destination-delete',

                'nationality-list',
                'nationality-create',
                'nationality-show',
                'nationality-edit',
                'nationality-delete',

                'activity-list',
                'activity-create',
                'activity-show',
                'activity-edit',
                'activity-delete',
            ]
        ],
        [
            "name" => "Hartawan Stabil",
            "default_route" => "panel.user-activity.index",
            "permissions" => [
                'dashboard-open',

                'reporting-list',
                'reporting-download',

                'user-management-open',

                'user-account-list',
                'user-account-create',
                'user-account-show',
                'user-account-edit',
                'user-account-delete',

                'user-approval-list',
                'user-approval-approve',

                'master-open',

                'destination-list',
                'destination-create',
                'destination-show',
                'destination-edit',
                'destination-delete',

                'nationality-list',
                'nationality-create',
                'nationality-show',
                'nationality-edit',
                'nationality-delete',

                'activity-list',
                'activity-create',
                'activity-show',
                'activity-edit',
                'activity-delete',
            ]
        ],
        [
            "name" => "PDRM",
            "default_route" => "panel.autho-my-dashboard.index",
            "permissions" => [
                'autho-my-dashboard-list',
                'autho-my-dashboard-show',
                'autho-my-dashboard-approve',
            ]
        ],
        [
            "name" => "Jabatan Laut",
            "default_route" => "panel.autho-my-dashboard.index",
            "permissions" => [
                'autho-my-dashboard-list',
                'autho-my-dashboard-show',
                'autho-my-dashboard-approve',
            ]
        ],
        [
            "name" => "Sabah Parks",
            "default_route" => "panel.autho-my-dashboard.index",
            "permissions" => [
                'autho-my-dashboard-list',
                'autho-my-dashboard-show',
                'autho-my-dashboard-approve',
            ]
        ],
        [
            "name" => "Jabatan Pelabuhan",
            "default_route" => "panel.autho-my-dashboard.index",
            "permissions" => [
                'autho-my-dashboard-list',
                'autho-my-dashboard-show',
                'autho-my-dashboard-approve',
            ]
        ],
        [
            "name" => "Agent",
            "default_route" => "panel.company-profile.step1.edit",
            "permissions" => [

                'user-activity-list',
                'user-activity-show',
                'user-activity-delete',

                'company-profile-show',
                'company-profile-create',
                'company-profile-edit',

                'manifest-information-open',

                'manifest-create',
                'manifest-show',
                'manifest-edit',
                'manifest-approval',

                'payment-list',
                'payment-show',
                'payment-edit',

                'support-list',
                'support-show',

                'user-management-open',

                'user-company-list',
                'user-company-create',
                'user-company-show',
                'user-company-edit',
                'user-company-delete',
            ]
        ],
        [
            "name" => "Jetty Operator",
            "default_route" => "panel.user-activity.index",
            "permissions" => [

                'user-activity-list',
                'user-activity-show',

                'payment-status-list',
                'payment-status-show',

                'reporting-list',
                'reporting-download',

                'user-management-open',

                'user-approval-list',
                'user-approval-approve',

                'user-jetty-list',
                'user-jetty-create',
                'user-jetty-show',
                'user-jetty-edit',
                'user-jetty-delete',
            ]
        ],
        [
            "name" => "Agent Employee",
            "default_route" => "panel.company-profile.step1.edit",
            "permissions" => [

                'user-activity-list',
                'user-activity-show',
                'user-activity-delete',

                'company-profile-show',
                'company-profile-create',
                'company-profile-edit',

                'manifest-information-open',

                'manifest-create',
                'manifest-show',
                'manifest-edit',
                'manifest-approval',

                'payment-list',
                'payment-show',
                'payment-edit',

                'support-list',
                'support-show',
            ]
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        DB::table("role_has_permissions")->truncate();

        foreach ($this->roles as $role) {
            $permissions = $role['permissions'] ?? [];
            unset($role["permissions"]);

            $modelRole = Role::create($role);

            if ($permissions) {
                $modelRole->givePermissionTo($permissions);
            }
        }
    }
}
