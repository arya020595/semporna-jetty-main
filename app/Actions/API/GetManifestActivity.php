<?php

namespace App\Actions\API;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\RefNationality;
use App\Helpers\DatatablesHelper;
use App\Models\Manifest;
use App\Models\RefDestination;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
                "label" => "Search",
                "name" => "search",
                "searchable" => true
            ],
            [
                "label" => "Approval Status",
                "name" => "status",
                "searchable" => true
            ],
            [
                "label" => "Departure Date",
                "name" => "departure_date",
                "searchable" => true
            ]
        ];
    }
    /**
     * Get the base query
     *
     * @return Builder
     */
    public function getQuery()
    {
        return Manifest::query()
            ->when($this->user->hasRole([User::ROLE_AGENT, User::ROLE_AGENT_EMPLOYEE]), function ($query) {
                return $query->where("company_id", $this->user->company_id);
            })
            ->when($this->user->hasRole(User::ROLE_OPERATOR_JETTY), function ($query) {
                return $query->where("departure_id", $this->user->jetty_id)
                    ->where("is_final", 1);
            })
            ->when($this->user->hasRole([User::ROLE_JABATAN_LAUT, User::ROLE_JABATAN_PELABUHAN, User::ROLE_PDRM, User::ROLE_SABAH_PARKS]), function ($query) {
                // Return only manifests for the authority's jetty, if they are bound to one
                if ($this->user->jetty_id) {
                    $query->where("departure_id", $this->user->jetty_id);
                }

                return $query->where("is_final", 1)
                    ->where("payment_status", Manifest::PAYMENT_STATUS_PAID);
            });
    }

    /**
     * Execute the action
     *
     * @param  array  $filters
     * @return LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        $this->user = $this->user ?: Auth::user();

        return $this->getQuery()
            ->with("destination")
            ->when($filters['search_fields'] ?? false, function (Builder $query) use ($filters) {
                $allowedFields = DatatablesHelper::getSearchableField($this->columns);
                foreach ($filters['search_fields'] as $key => $column) {
                    if (!in_array($column, $allowedFields)) {
                        continue;
                    }

                    $search = $filters['search_values'][$key] ?? '';

                    if ($search === '') {
                        continue;
                    }


                    if ($column == 'status') {
                        $query->where($column, $search);
                        continue;
                    }

                    if ($column == 'departure_date') {
                        $query->whereDate($column, $search);
                        continue;
                    }

                    $query->where(function ($query) use ($search) {
                        return $query->where("company_name", 'like', '%' . $search . '%')
                            ->orWhere("boat_number", 'like', '%' . $search . '%')
                            ->orWhere("form_number", 'like', '%' . $search . '%');
                    });
                }
            })
            // ->orderBy($filters["order_by"] ?? 'created_at', $filters["order_type"] ?? 'desc')
            ->orderBy($filters["order_by"] ?? 'departure_date', $filters["order_type"] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    /**
     * Get min and max date for the user's scope
     * 
     * @param array $filters
     * @return object
     */
    public function getDateRange(array $filters = [])
    {
        $this->user = $this->user ?: Auth::user();

        return $this->getQuery()
            ->when($filters['search_fields'] ?? false, function (Builder $query) use ($filters) {
                $allowedFields = DatatablesHelper::getSearchableField($this->columns);
                foreach ($filters['search_fields'] as $key => $column) {
                    if (!in_array($column, $allowedFields)) {
                        continue;
                    }

                    if ($column == 'departure_date') {
                        continue;
                    }

                    $search = $filters['search_values'][$key] ?? '';

                    if ($search === '') {
                        continue;
                    }


                    if ($column == 'status') {
                        $query->where($column, $search);
                        continue;
                    }

                    $query->where(function ($query) use ($search) {
                        return $query->where("company_name", 'like', '%' . $search . '%')
                            ->orWhere("boat_number", 'like', '%' . $search . '%')
                            ->orWhere("form_number", 'like', '%' . $search . '%');
                    });
                }
            })
            ->selectRaw("MIN(departure_date) as min_date, MAX(departure_date) as max_date")
            ->first();
    }

    /**
     * Set authenticated User
     * 
     * @param User
     * @return $this
     */
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
