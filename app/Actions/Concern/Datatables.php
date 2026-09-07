<?php

namespace App\Actions\Concern;

use App\Helpers\DatatablesHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class Datatables
{
    const TYPE_CUSTOM_ACTION = 'custom-action';
    const TYPE_ACTION = 'action';
    const TYPE_DATE_FORMAT = 'date-format';
    const TYPE_LINK = 'link';
    const TYPE_MULTICOLUMN = 'multicolumn';

    protected Model $models;

    protected $columns = [
        [
            "label" => "",
            "name" => "action",
            "class" => "text-nowrap"
        ],
        [
            "label" => "Code",
            "name" => "code",
            "orderable" => true,
            "searchable" => true
        ],
        [
            "label" => "Updated At",
            "name" => "updated_at",
            "orderable" => true,
            "isDateTime" => true,
        ]
    ];

    protected $defaultOrder = "updated_at";

    /**
     * Execute the action
     *
     * @param  array  $data
     * @return LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        return $this->models->query()
            ->when($filters['search_fields'] ?? false, function ($query) use ($filters) {
                $allowedFields = DatatablesHelper::getSearchableField($this->columns);
                foreach ($filters['search_fields'] as $key => $column) {
                    if (!in_array($column, $allowedFields)) {
                        continue;
                    }

                    $query->when($filters['search_values'][$key] ?? false, function ($query, $search) use ($column) {
                        $query->where($column, 'like', '%' . $search . '%');
                    });
                }
            })
            ->orderBy($filters["order_by"] ?? $this->defaultOrder, $filters["order_type"] ?? 'asc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function getColumns()
    {
        return $this->columns;
    }
}
