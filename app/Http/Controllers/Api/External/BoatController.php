<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\BoatResource;
use App\Models\Boat;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class BoatController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['sort' => 'sometimes|required|string']);

        $boats = QueryBuilder::for(Boat::class, $request)
            ->defaultSort('number')
            ->allowedSorts('number')
            ->get(['id', 'company_id', 'number']);

        return response()->json([
            'success' => true,
            'message' => 'List Boats',
            'data' => BoatResource::collection($boats),
        ]);
    }
}
