<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\ActivityResource;
use App\Models\RefActivity;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'filter' => 'sometimes|array',
            'sort' => 'sometimes|required|string',
        ]);

        $items = QueryBuilder::for(RefActivity::class, $request)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('code'),
                AllowedFilter::partial('title'),
            ])
            ->defaultSort('title')
            ->allowedSorts('id', 'code', 'title')
            ->get(['id', 'code', 'title']);

        return response()->json([
            'success' => true,
            'message' => 'List Activities',
            'data' => ActivityResource::collection($items),
        ]);
    }
}
