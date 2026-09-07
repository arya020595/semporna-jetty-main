<?php

namespace App\Actions\CompanyManifest;

use App\Models\ManifestFee;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPaymentHistory
{
    protected User $user;

    protected $columns;

    public function __construct()
    {
        $this->columns = [
            [
                'label'     => 'Payment Code',
                'name'      => 'code',
                // 'orderable' => true,
                'searchable' => true,
            ],
            [
                'label'     => 'Departure Date',
                'name'      => 'departure_date',
                'orderable' => false,
                // 'searchable' => true,
                // 'searchtype' => 'date',
            ],
            [
                'label'     => 'Transaction Date',
                'name'      => 'created_at',
                'orderable' => true,
                'searchable' => false,
            ],
            [
                'label'     => 'Payer',
                'name'      => 'first_name',
                // 'orderable' => true,
                'searchable' => true,
            ],
            [
                'label'     => 'Manifests',
                'name'      => 'manifest_count',
                'orderable' => false,
                'searchable' => false,
            ],
            [
                'label'     => 'Total Amount',
                'name'      => 'amount',
                // 'orderable' => true,
                'searchable' => false,
            ],
            [
                'label'     => 'Status',
                'name'      => 'status',
                // 'orderable' => true,
                'searchable' => true,
                'searchtype' => 'select',
                'options'   => [
                    ['id' => Payment::STATUS_PAID,    'label' => 'Paid'],
                    ['id' => Payment::STATUS_PENDING, 'label' => 'Pending'],
                    ['id' => Payment::STATUS_FAILED,  'label' => 'Failed'],
                ],
            ],
        ];
    }

    /**
     * Execute the action.
     *
     * @param  array  $filters
     * @return LengthAwarePaginator
     */
    public function execute(array $filters): LengthAwarePaginator
    {
        $query = Payment::with([
            'manifestFee' => function ($q) {
                $q->with([
                    'manifest' => function ($mq) {
                        $mq->with(['manifestDestination', 'departure']);
                    }
                ]);
            },
            'user',
        ]);

        // Role scope: Agent can only see their own payments
        if (isset($this->user) && $this->user->hasRole(User::ROLE_AGENT)) {
            $query->where('user_id', $this->user->id);
        }

        // Role scope: Jetty Operator can only see payments affecting their jetty
        if (isset($this->user) && $this->user->hasRole(User::ROLE_OPERATOR_JETTY)) {
            $query->whereHas('manifestFee.manifest', function ($q) {
                $q->where('departure_id', $this->user->jetty_id);
            });
        }

        // Search filters
        $searchFields = $filters['search_fields'] ?? [];
        $searchValues = $filters['search_values'] ?? [];

        foreach ($searchFields as $key => $field) {
            $value = $searchValues[$key] ?? '';
            if ($value === '' || $value === null) {
                continue;
            }

            if ($field === 'status') {
                $query->where('status', $value);
            } elseif ($field === 'code' || $field === 'first_name') {
                $query->where($field, 'like', '%' . $value . '%');
            }
        }

        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $query->whereHas('manifestFee.manifest', function ($q) use ($filters) {
                if (!empty($filters['date_from'])) {
                    $q->where('departure_date', '>=', $filters['date_from']);
                }
                if (!empty($filters['date_to'])) {
                    $q->where('departure_date', '<=', $filters['date_to']);
                }
            });
        }

        $orderBy   = $filters['order_by']   ?? 'created_at';
        $orderType = $filters['order_type'] ?? 'desc';
        $query->orderBy($orderBy, $orderType);

        return $query->paginate($filters['per_page'] ?? 20)->appends($filters);
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
