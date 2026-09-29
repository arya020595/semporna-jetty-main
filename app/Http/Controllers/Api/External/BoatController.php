<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\BoatResource;
use App\Models\Boat;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BoatController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'filter' => 'sometimes|array',
            'sort' => 'sometimes|required|string',
        ]);

        $boats = QueryBuilder::for(Boat::class, $request)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::partial('number'),
            ])
            ->with(['company:id,name', 'boatman'])
            ->defaultSort('number')
            ->allowedSorts('id', 'number')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List Boats',
            'data' => BoatResource::collection($boats),
        ]);
    }
}
