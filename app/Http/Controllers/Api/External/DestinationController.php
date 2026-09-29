<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\DestinationResource;
use App\Models\RefDestination;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'filter' => 'sometimes|array',
            'sort' => 'sometimes|required|string',
        ]);

        $items = QueryBuilder::for(RefDestination::class, $request)
            ->where('type', RefDestination::TYPE_DESTINATION)
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
            'message' => 'List Destinations',
            'data' => DestinationResource::collection($items),
        ]);
    }
}
