<?php

namespace App\Actions\User;

use App\Helpers\DatatablesHelper;
use App\Models\User;
use App\Models\UserAccessLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class GetUsersDatatables
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
                "searchable" => true,
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
                        "label" => "Non Active"
                    ]
                ]
            ],
            [
                "label" => "Last Access",
                "name" => "last_access",
                "orderable" => true,
                "isDateTime" => true,
                "class" => "text-nowrap"
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

        return User::with(["roles", "jetty"])
            ->leftJoin($mdlAccessLog->getTable(), $mdlAccessLog->qualifyColumn("user_id"), "=", $mdlUser->qualifyColumn("id"))
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
            ->when($this->user->hasRole(User::ROLE_OPERATOR_JETTY), function ($query) use ($mdlUser) {
                return $query->where($mdlUser->qualifyColumn("jetty_id"), $this->user->jetty_id)
                    ->whereHas('roles', function ($query) {
                        $query->where('roles.id', User::ROLE_OPERATOR_JETTY);
                    });
            })
            ->groupBy(
                $mdlUser->qualifyColumn("id"),
                $mdlUser->qualifyColumn("staf_id"),
                $mdlUser->qualifyColumn("name"),
                $mdlUser->qualifyColumn("email"),
                $mdlUser->qualifyColumn("status"),
                $mdlUser->qualifyColumn("jetty_id"),
                $mdlUser->qualifyColumn("created_at"),
            )
            ->select(
                $mdlUser->qualifyColumn("id"),
                $mdlUser->qualifyColumn("staf_id"),
                $mdlUser->qualifyColumn("name"),
                $mdlUser->qualifyColumn("email"),
                $mdlUser->qualifyColumn("status"),
                $mdlUser->qualifyColumn("jetty_id"),
                $mdlUser->qualifyColumn("created_at"),
                DB::raw('MAX(' . $mdlAccessLog->qualifyColumn('created_at') . ') as last_access'),
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
