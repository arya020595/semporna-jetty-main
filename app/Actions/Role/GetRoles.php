<?php

namespace App\Actions\Role;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class GetRoles
{
    /**
     * Execute the action
     *
     * @param  array  $data
     * @return LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        return Role::query()
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['search'] . '%');
            })
            ->orderBy($filters["order_by"] ?? 'name', $filters["order_type"] ?? 'asc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }
}
