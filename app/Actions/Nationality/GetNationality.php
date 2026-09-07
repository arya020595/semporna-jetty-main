<?php

namespace App\Actions\Nationality;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\RefNationality;
use App\Helpers\DatatablesHelper;

class GetNationality
{
    protected $columns;

    public function __construct()
    {
        $this->columns = [
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
                "label" => "Nationality",
                "name" => "title",
                "orderable" => true,
                "searchable" => true
            ],
            [
                "label" => "Updated At",
                "name" => "updated_at",
                "orderable" => true,
                "searchable" => false,
                "isDateTime" => true,
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
        return RefNationality::query()
            ->when($filters['search_fields'] ?? false, function ($query) use ($filters) {
                $allowedFields = DatatablesHelper::getSearchableField($this->columns);
                foreach ($filters['search_fields'] as $key => $column) {
                    if (!in_array($column, $allowedFields))
                        continue;
                    $search = $filters['search_values'][$key] ?? '';
                    $query->when($search !== '', function ($query) use ($column, $search) {
                        $query->where($column, 'like', '%' . $search . '%');
                    });
                }
            })
            ->orderBy($filters["order_by"] ?? 'title', $filters["order_type"] ?? 'asc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function getColumns()
    {
        return $this->columns;
    }
}
