<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class MenuSeeder extends Seeder
{
    protected $arrMenu = [
        [
            "parent_id" => 0,
            "code" => "dashboard",
            "name" => "Dashboard",
            "description" => "",
            "icon" => "dashboard",
            "order" => '01',
            "type" => 0,
            "permission" => [
                'dashboard-open'
            ]
        ],
        [
            "parent_id" => 0,
            "code" => "user-activity",
            "name" => "Activity",
            "description" => "",
            "icon" => "table_rows",
            "order" => '02',
            "type" => 0,
            "permission" => [
                'user-activity-list',
                'user-activity-show',
                'user-activity-approve',
                'user-activity-delete'
            ]
        ],
        [
            "parent_id" => 0,
            "code" => "manifest-information",
            "name" => "Manifest Information",
            "description" => "",
            "icon" => "description",
            "order" => '03',
            "type" => 2,
            "permission" => [
                'manifest-information-open',
            ],
            "submenu" => [
                [
                    "parent_id" => 0,
                    "code" => "manifest",
                    "name" => "Manifest Form",
                    "description" => "",
                    "icon" => "description",
                    "order" => '01',
                    "type" => 0,
                    "permission" => [
                        'manifest-create',
                        'manifest-show',
                        'manifest-edit',
                        'manifest-approval',
                    ]
                ],
                [
                    "parent_id" => 0,
                    "code" => "payment",
                    "name" => "Payment Form",
                    "description" => "",
                    "icon" => "description",
                    "order" => '02',
                    "type" => 0,
                    "permission" => [
                        'payment-list',
                        'payment-show',
                        'payment-edit',
                    ]
                ],
            ]
        ],
        [
            "parent_id" => 0,
            "code" => "report",
            "name" => "Reporting",
            "description" => "",
            "icon" => "description",
            "order" => '04',
            "type" => 0,
            "permission" => [
                'reporting-list',
                'reporting-download'
            ]
        ],
        [
            "parent_id" => 0,
            "code" => "payment-status",
            "name" => "Payment Status",
            "description" => "",
            "icon" => "credit_card",
            "order" => '04',
            "type" => 0,
            "permission" => [
                'payment-status-list',
                'payment-status-show'
            ]
        ],
        [
            "parent_id" => 0,
            "code" => "company-profile",
            "name" => "Profile",
            "description" => "",
            "icon" => "person",
            "order" => '04',
            "type" => 0,
            "permission" => [
                'company-profile-show',
                'company-profile-create',
                'company-profile-edit'
            ]
        ],
        [
            "parent_id" => 0,
            "code" => "support",
            "name" => "Support",
            "description" => "",
            "icon" => "call",
            "order" => '04',
            "type" => 0,
            "permission" => [
                'support-list',
                'support-show',
            ]
        ],
        [
            "parent_id" => 0,
            "code" => "master",
            "name" => "References Table",
            "description" => "",
            "icon" => "list",
            "order" => '06',
            "type" => 2,
            "permission" => [
                'master-open'
            ],
            "submenu" => [
                [
                    "parent_id" => 0,
                    "code" => "destination",
                    "name" => "Island",
                    "description" => "",
                    "icon" => "",
                    "order" => '02',
                    "type" => 0,
                    "permission" => [
                        'destination-list',
                        'destination-create',
                        'destination-show',
                        'destination-edit',
                        'destination-delete',
                    ],
                ],
                [
                    "parent_id" => 0,
                    "code" => "nationality",
                    "name" => "Nationality",
                    "description" => "",
                    "icon" => "",
                    "order" => '01',
                    "type" => 0,
                    "permission" => [
                        'nationality-list',
                        'nationality-create',
                        'nationality-show',
                        'nationality-edit',
                        'nationality-delete',
                    ],
                ],
                [
                    "parent_id" => 0,
                    "code" => "activity",
                    "name" => "Activity",
                    "description" => "",
                    "icon" => "",
                    "order" => '02',
                    "type" => 0,
                    "permission" => [
                        'activity-list',
                        'activity-create',
                        'activity-show',
                        'activity-edit',
                        'activity-delete',
                    ],
                ],
            ]
        ],
        [
            "parent_id" => 0,
            "code" => "user-management",
            "name" => "User Management",
            "description" => "",
            "icon" => "admin_panel_settings",
            "order" => '07',
            "type" => 2,
            "permission" => [
                'user-management-open'
            ],
            "submenu" => [
                [
                    "parent_id" => 0,
                    "code" => "role",
                    "name" => "User Access Role",
                    "description" => "",
                    "icon" => "",
                    "order" => '02',
                    "type" => 0,
                    "permission" => [
                        'role-list',
                        'role-create',
                        'role-show',
                        'role-edit',
                        'role-delete',
                    ],
                ],
                [
                    "parent_id" => 0,
                    "code" => "user-account",
                    "name" => "User Profile",
                    "description" => "",
                    "icon" => "",
                    "order" => '01',
                    "type" => 0,
                    "permission" => [
                        'user-account-list',
                        'user-account-create',
                        'user-account-show',
                        'user-account-edit',
                        'user-account-delete',
                        'user-account-approval'
                    ],
                ],
                [
                    "parent_id" => 0,
                    "code" => "user-jetty",
                    "name" => "User Jetty",
                    "description" => "",
                    "icon" => "",
                    "order" => '01',
                    "type" => 0,
                    "permission" => [
                        'user-jetty-list',
                        'user-jetty-create',
                        'user-jetty-show',
                        'user-jetty-edit',
                        'user-jetty-delete',
                    ],
                ],
                [
                    "parent_id" => 0,
                    "code" => "user-company",
                    "name" => "User Operator",
                    "description" => "",
                    "icon" => "",
                    "order" => '01',
                    "type" => 0,
                    "permission" => [
                        'user-company-list',
                        'user-company-create',
                        'user-company-show',
                        'user-company-edit',
                        'user-company-delete',
                    ],
                ],
                [
                    "parent_id" => 0,
                    "code" => "user-approval",
                    "name" => "User Approval",
                    "description" => "",
                    "icon" => "",
                    "order" => '01',
                    "type" => 0,
                    "permission" => [
                        'user-approval-list',
                        'user-approval-approve'
                    ],
                ],
            ]
        ],

        // Authorities
        [
            "parent_id" => 0,
            "code" => "autho-my-dashboard",
            "name" => "My Dashboard",
            "description" => "",
            "icon" => "dashboard",
            "order" => '01',
            "type" => 0,
            "permission" => [
                'autho-my-dashboard-list',
                'autho-my-dashboard-show',
                'autho-my-dashboard-approve',
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
        Menu::truncate();
        Permission::truncate();
        DB::table('menu_has_permission')->truncate();

        foreach ($this->arrMenu as $menu) {
            $permissions = collect($menu['permission']);
            $submenus = $menu['submenu'] ?? [];

            unset($menu["permission"]);
            unset($menu["submenu"]);

            $menu["order"] = $menu["order"] . "00";
            $menu = Menu::create($menu);
            $permissions = $permissions->map(function ($item) {
                return [
                    'name' => $item
                ];
            });

            foreach ($permissions as $permission) {
                $menu->permission()->create($permission);
            }

            foreach ($submenus as $submenu) {
                $permissions = collect($submenu['permission']);
                $subSubmenus = $submenu['submenu'] ?? [];

                unset($submenu["permission"]);
                unset($submenu["submenu"]);

                $submenu["order"] = substr($menu->order, 0, 2) . $submenu["order"];
                $submenu['parent_id'] = $menu->id;
                $submenu = Menu::create($submenu);
                $permissions = $permissions->map(function ($item) {
                    return [
                        'name' => $item
                    ];
                });

                foreach ($permissions as $permission) {
                    $submenu->permission()->create($permission);
                }


                foreach ($subSubmenus as $subSubmenu) {
                    $permissions = collect($subSubmenu['permission']);

                    unset($subSubmenu["permission"]);

                    $subSubmenu["order"] = substr($submenu->order, 0, 4) . $subSubmenu["order"];
                    $subSubmenu['parent_id'] = $submenu->id;
                    $subSubmenu = Menu::create($subSubmenu);
                    $permissions = $permissions->map(function ($item) {
                        return [
                            'name' => $item
                        ];
                    });

                    foreach ($permissions as $permission) {
                        $subSubmenu->permission()->create($permission);
                    }
                }
            }
        }
    }
}
