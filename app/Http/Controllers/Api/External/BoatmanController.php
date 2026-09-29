<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\BoatmanResource;
use App\Models\Boatman;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BoatmanController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'filter' => 'sometimes|array',
            'sort' => 'sometimes|required|string',
        ]);

        $boatmen = QueryBuilder::for(Boatman::class, $request)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('boat_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('type'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('ic_no'),
            ])
            ->with('company:id,name')
            ->defaultSort('name')
            ->allowedSorts('id', 'name', 'type')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List Boatmen',
            'data' => BoatmanResource::collection($boatmen),
        ]);
    }
}
