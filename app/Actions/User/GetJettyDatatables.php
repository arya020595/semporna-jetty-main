<?php

namespace App\Actions\User;

use App\Helpers\DatatablesHelper;
use App\Models\User;
use App\Models\UserAccessLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class GetJettyDatatables
{
    protected $columns;

    /**
     * @var User
     */
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();

        $this->columns = [
            [
                "label" => "",
                "name" => "action",
                "class" => "text-nowrap"
            ],
            [
                "label" => "User ID",
                "name" => "staf_id",
                "orderable" => true,
                "searchable" => true
            ],
            [
                "label" => "Name",
                "name" => "name",
                "orderable" => true,
                "searchable" => true
            ],
            [
                "label" => "Email",
                "name" => "email",
                "orderable" => true,
                "searchable" => true
            ],
            [
                "label" => "Roles",
                "name" => "roles",
                "searchable" => !$this->user->hasRole(User::ROLE_OPERATOR_JETTY),
                "searchtype" => "select",
                "options" => Role::all()->map(function ($item) {
                    return [
                        "id" => $item->id,
                        "label" => $item->name
                    ];
                })
            ],
            [
                "label" => "Status",
                "name" => "status",
                "orderable" => true,
                "searchable" => true,
                "searchtype" => "select",
                "options" => [
                    [
                        "id" => 1,
                        "label" => "Active"
                    ],
                    [
                        "id" => 0,
                        "label" => "Pending"
                    ],
                    [
                        "id" => -1,
                        "label" => "Rejected"
                    ]
                ]
            ],
            [
                "label" => "Register At",
                "name" => "created_at",
                "orderable" => true,
                "isDateTime" => true,
                "class" => "text-nowrap"
            ]
        ];
    }

    /**
     * Execute the action
     *
     * @param  array  $data
     * @return LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        $mdlAccessLog = new UserAccessLog();
        $mdlUser = new User();

        return User::query()
            ->when($filters['search_fields'] ?? false, function ($query) use ($filters) {
                $allowedFields = DatatablesHelper::getSearchableField($this->columns);
                foreach ($filters['search_fields'] as $key => $column) {
                    if (!in_array($column, $allowedFields))
                        continue;

                    $search = $filters['search_values'][$key] ?? '';
                    $query->when($search !== '', function ($query) use ($column, $search) {
                        if ($column == 'roles') {
                            $query->whereHas('roles', function ($query) use ($search) {
                                $query->where('roles.id', $search);
                            });
                        } else if ($column == 'status') {
                            $query->where($column, $search);
                        } else {
                            $query->where($column, 'like', '%' . $search . '%');
                        }
                    });
                }
            })
            ->where($mdlUser->qualifyColumn("jetty_id"), $this->user->jetty_id)
            ->whereHas('roles', function ($query) {
                $query->where('roles.id', User::ROLE_OPERATOR_JETTY);
            })
            ->select(
                $mdlUser->qualifyColumn("id"),
                $mdlUser->qualifyColumn("staf_id"),
                $mdlUser->qualifyColumn("jetty_id"),
                $mdlUser->qualifyColumn("name"),
                $mdlUser->qualifyColumn("email"),
                $mdlUser->qualifyColumn("status"),
                $mdlUser->qualifyColumn("created_at"),
            )
            ->orderBy($filters["order_by"] ?? $mdlUser->qualifyColumn("updated_at"), $filters["order_type"] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function getColumns()
    {
        return $this->columns;
    }
}
