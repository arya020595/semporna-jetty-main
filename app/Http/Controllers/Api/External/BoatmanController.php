<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\BoatmanResource;
use App\Models\Boatman;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class BoatmanController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['sort' => 'sometimes|required|string']);

        $boatmen = QueryBuilder::for(Boatman::class, $request)
            ->defaultSort('name')
            ->allowedSorts('name')
            ->get(['id', 'boat_id', 'company_id', 'name', 'ic_no', 'type']);

        return response()->json([
            'success' => true,
            'message' => 'List Boatmen',
            'data' => BoatmanResource::collection($boatmen),
        ]);
    }
}
