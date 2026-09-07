<?php

namespace App\Actions\CompanyManifest;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\DatatablesHelper;
use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\RefDestination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetManifestPayment
{
    protected $columns;

    protected User $user;

    public function __construct()
    {
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
                "label" => "Overall Fee",
                "name" => "total",
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
        ];

        $this->user = Auth::user();
    }
    /**
     * Execute the action
     *
     * @param  array  $data
     * @return LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        $mdlManifest = new Manifest();

        return Manifest::with("manifestDestination", "manifestFee", "departure")
            ->when($filters['search_fields'] ?? false, function ($query) use ($filters) {
                $allowedFields = DatatablesHelper::getSearchableField($this->columns);
                foreach ($filters['search_fields'] as $key => $column) {
                    if (!in_array($column, $allowedFields)) {
                        continue;
                    }
                    $search = $filters['search_values'][$key] ?? '';

                    if ($column == "destination" && $search !== '') {
                        return $query->whereHas("manifestDestination", function ($query) use ($search) {
                            return $query->where("ref_destination_id", $search);
                        });
                    }

                    $query->when($search !== '', function ($query) use ($column, $search) {
                        $query->where($column, 'like', '%' . $search . '%');
                    });
                }
            })
            ->where($mdlManifest->qualifyColumn("is_final"), 1)
            ->when(optional($this->user)->hasRole(User::ROLE_AGENT), function ($query) {
                return $query->where("user_id", $this->user->id);
            })
            ->when(optional($this->user)->hasRole(User::ROLE_OPERATOR_JETTY), function ($query) {
                return $query->where("departure_id", $this->user->jetty_id);
            })
            ->where("is_final", 1)
            ->orderByRaw("
                CASE
                    WHEN payment_status = " . Manifest::PAYMENT_STATUS_PENDING . " AND departure_date >= '" . now('Asia/Kuala_Lumpur')->format('Y-m-d') . "' THEN 1
                    WHEN payment_status = " . Manifest::PAYMENT_STATUS_PENDING . " AND departure_date < '" . now('Asia/Kuala_Lumpur')->format('Y-m-d') . "' THEN 2
                    WHEN payment_status = " . Manifest::PAYMENT_STATUS_PAID . " THEN 3
                    ELSE 4
                END
            ")
            ->orderBy($filters["order_by"] ?? 'created_at', $filters["order_type"] ?? 'desc')
            ->select(
                $mdlManifest->qualifyColumn("id"),
                $mdlManifest->qualifyColumn("uuid"),
                $mdlManifest->qualifyColumn("form_number"),
                $mdlManifest->qualifyColumn("company_name"),
                $mdlManifest->qualifyColumn("is_final"),
                $mdlManifest->qualifyColumn("departure_date"),
                $mdlManifest->qualifyColumn("departure_id"),
                $mdlManifest->qualifyColumn("boat_number"),
                $mdlManifest->qualifyColumn("payment_status")
            )
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
}
