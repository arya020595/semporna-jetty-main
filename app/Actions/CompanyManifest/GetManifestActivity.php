<?php

namespace App\Actions\CompanyManifest;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\RefNationality;
use App\Helpers\DatatablesHelper;
use App\Models\Manifest;
use App\Models\RefDestination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class GetManifestActivity
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
                "label" => "Number",
                "name" => "form_number",
                "orderable" => true,
                "searchable" => true
            ],
            [
                "label" => "Departure Date",
                "name" => "departure_date",
                "orderable" => true,
                "searchable" => true,
                "searchtype" => 'date',
            ],
            [
                "label" => "Company",
                "name" => "company_name",
                "orderable" => true,
                "searchable" => true
            ],
            [
                "label" => "Boat No.",
                "name" => "boat_number",
                "orderable" => true,
                "searchable" => true
            ],
            [
                "label" => "Destination",
                "name" => "destination",
                "searchable" => true,
                "searchtype" => 'select',
                'options' => RefDestination::query()
                    ->where("type", RefDestination::TYPE_DESTINATION)
                    ->get()->map(function ($item) {
                        return [
                            "id" => $item->id,
                            "label" => $item->title
                        ];
                    })
            ],
            [
                "label" => "No. Of Guest",
                "name" => "guest_count",
                "orderable" => false,
                "searchable" => false
            ],
            [
                "label" => "No. Of Staff",
                "name" => "staff_count",
                "orderable" => false,
                "searchable" => false
            ],
            [
                "label" => "Payment Status",
                "name" => "payment_status",
                "orderable" => true,
                "searchable" => true,
                "searchtype" => 'select',
                'options' => [
                    [
                        "id" => Manifest::PAYMENT_STATUS_PENDING,
                        "label" => "Pending"
                    ],
                    [
                        "id" => Manifest::PAYMENT_STATUS_PAID,
                        "label" => "Paid",
                    ],
                ]
            ],
            [
                "label" => "Approval Status",
                "name" => "status",
                "orderable" => true,
                "searchable" => true,
                "isHtml" => true,
                "searchtype" => 'select',
                'options' => [
                    [
                        "id" => Manifest::STATUS_PENDING,
                        "label" => "Pending",
                    ],
                    [
                        "id" => Manifest::STATUS_APPROVED_PROGRESS,
                        "label" => "In Progress",
                    ],
                    [
                        "id" => Manifest::STATUS_APPROVED,
                        "label" => "Approved",
                    ],
                    [
                        "id" => Manifest::STATUS_AMEND,
                        "label" => "Amend",
                    ],
                    [
                        "id" => Manifest::STATUS_REJECTED,
                        "label" => "Rejected",
                    ],
                ]
            ],
            [
                "label" => "Verification by Jetty Operator Status (Yes or No)",
                "name" => "jetty_approval_status",
                "isHtml" => true,
                "orderable" => true,
                "searchable" => true,
                "searchtype" => 'select',
                'options' => [
                    [
                        "id" => Manifest::STATUS_PENDING,
                        "label" => "Pending",
                    ],
                    [
                        "id" => Manifest::STATUS_APPROVED,
                        "label" => "Approved",
                    ],
                    [
                        "id" => Manifest::STATUS_REJECTED,
                        "label" => "Rejected",
                    ],
                ]
            ],
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
        return Manifest::with("destination", "departure")
            ->when($filters['search_fields'] ?? false, function ($query) use ($filters) {
                $allowedFields = DatatablesHelper::getSearchableField($this->columns);
                foreach ($filters['search_fields'] as $key => $column) {
                    if (!in_array($column, $allowedFields)) {
                        continue;
                    }

                    $search = $filters['search_values'][$key] ?? '';

                    if ($search === '') {
                        continue;
                    }

                    if ($column == "destination" && $search !== '') {
                        $query->whereHas("manifestDestination", function ($query) use ($search) {
                            return $query->where("ref_destination_id", $search);
                        });
                        continue;
                    }

                    if ($column == 'status') {
                        $authorityRoles = [User::ROLE_JABATAN_LAUT, User::ROLE_JABATAN_PELABUHAN, User::ROLE_PDRM, User::ROLE_SABAH_PARKS];
                        if ($this->user->hasRole($authorityRoles)) {
                            $roleId = $this->user->activeRole()->id;
                            $query->where(function ($q) use ($roleId, $search) {
                                if ($search == Manifest::STATUS_PENDING) {
                                    $q->whereDoesntHave('approvement', function ($sq) use ($roleId) {
                                        $sq->where('role_id', $roleId);
                                    })->orWhereHas('approvement', function ($sq) use ($roleId, $search) {
                                        $sq->where('role_id', $roleId)->where('status', $search);
                                    });
                                } else {
                                    $q->whereHas('approvement', function ($sq) use ($roleId, $search) {
                                        $sq->where('role_id', $roleId)->where('status', $search);
                                    });
                                }
                            });
                        } else {
                            $query->where($column, $search);
                        }
                        continue;
                    }

                    if ($column === 'jetty_approval_status') {
                        $query->where($column, $search);
                        continue;
                    }

                    if ($column == 'departure_date') {
                        $query->where(DB::raw("DATE(departure_date)"), $search);
                        continue;
                    }

                    $query->where($column, 'like', '%' . $search . '%');
                }
            })
            ->when($this->user->hasRole([User::ROLE_AGENT, User::ROLE_AGENT_EMPLOYEE]), function ($query) {
                return $query->where("company_id", $this->user->company_id);
            })
            ->when($this->user->hasRole(User::ROLE_OPERATOR_JETTY), function ($query) {
                return $query->where("departure_id", $this->user->jetty_id)
                    ->where("is_final", 1);
            })
            ->when($this->user->hasRole([User::ROLE_JABATAN_LAUT, User::ROLE_JABATAN_PELABUHAN, User::ROLE_PDRM, User::ROLE_SABAH_PARKS]), function ($query) {
                return $query->where("is_final", 1)
                    ->where("payment_status", Manifest::PAYMENT_STATUS_PAID);
            })
            ->orderBy($filters["order_by"] ?? 'departure_date', $filters["order_type"] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }
}
