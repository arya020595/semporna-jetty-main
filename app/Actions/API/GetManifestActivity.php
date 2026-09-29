<?php

namespace App\Actions\API;

use App\Models\Manifest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetManifestActivity
{
    /**
     * Filters that narrow by departure date. Left out of the min/max date range,
     * which describes the dates available to pick from.
     */
    const DATE_FILTERS = ['departure_date', 'departure_date_from', 'departure_date_to'];

    /**
     * Legacy `search_fields[]` / `search_values[]` names that map onto a filter;
     * any other name is ignored.
     */
    const LEGACY_SEARCH_FIELDS = ['search', 'status', 'departure_date'];

    /**
     * @var User
     */
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Get the base query, scoped to what the user is allowed to see
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
     * @param  array  $filters  validated request input
     * @return LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        $this->user = $this->user ?: Auth::user();

        return $this->queryBuilder($this->getQuery(), $this->toQueryRequest($filters))
            ->with("destination")
            ->defaultSort('-departure_date')
            ->allowedSorts('departure_date', 'created_at', 'updated_at', 'form_number', 'company_name', 'boat_number', 'status', 'payment_status')
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

        $request = $this->toQueryRequest($filters);
        $request->query->set('filter', Arr::except($request->query('filter', []), self::DATE_FILTERS));

        return $this->queryBuilder($this->getQuery(), $request)
            ->selectRaw("MIN(departure_date) as min_date, MAX(departure_date) as max_date")
            ->first();
    }

    /**
     * Filters accepted by the list, as `filter[<name>]=<value>`
     */
    protected function queryBuilder(Builder $query, Request $request): QueryBuilder
    {
        return QueryBuilder::for($query, $request)
            ->allowedFilters([
                // Free text over company name, boat number and form number
                AllowedFilter::callback('search', function (Builder $query, $value) {
                    // Spatie splits values on commas; put a comma typed by the user back
                    $search = implode(',', Arr::wrap($value));

                    $query->where(function (Builder $query) use ($search) {
                        $query->where("company_name", 'like', '%' . $search . '%')
                            ->orWhere("boat_number", 'like', '%' . $search . '%')
                            ->orWhere("form_number", 'like', '%' . $search . '%');
                    });
                }),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('payment_status'),
                AllowedFilter::callback('departure_date', function (Builder $query, $value) {
                    $query->whereDate('departure_date', $value);
                }),
                AllowedFilter::callback('departure_date_from', function (Builder $query, $value) {
                    $query->whereDate('departure_date', '>=', $value);
                }),
                AllowedFilter::callback('departure_date_to', function (Builder $query, $value) {
                    $query->whereDate('departure_date', '<=', $value);
                }),
            ]);
    }

    /**
     * Build the request Spatie reads `filter` and `sort` from. Also accepts the
     * legacy `search_fields[]` / `search_values[]` pairs the mobile app already
     * sends; an explicit `filter[...]` wins over its legacy equivalent.
     */
    protected function toQueryRequest(array $filters): Request
    {
        $filter = [];

        foreach ($filters['search_fields'] ?? [] as $key => $field) {
            $value = $filters['search_values'][$key] ?? '';

            if (in_array($field, self::LEGACY_SEARCH_FIELDS, true) && $value !== '') {
                $filter[$field] = $value;
            }
        }

        $query = ['filter' => array_merge($filter, $filters['filter'] ?? [])];

        if (isset($filters['sort'])) {
            $query['sort'] = $filters['sort'];
        } elseif (isset($filters['order_type'])) {
            // Legacy: direction only, always over departure date
            $query['sort'] = (strtolower($filters['order_type']) === 'asc' ? '' : '-') . 'departure_date';
        }

        return Request::create('/', 'GET', $query);
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
}
