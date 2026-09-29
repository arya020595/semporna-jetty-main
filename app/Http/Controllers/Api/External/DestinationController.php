<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\DestinationResource;
use App\Models\RefDestination;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['sort' => 'sometimes|required|string']);

        $destinations = QueryBuilder::for(RefDestination::class, $request)
            ->where('type', RefDestination::TYPE_DESTINATION)
            ->defaultSort('title')
            ->allowedSorts('title')
            ->get(['id', 'code', 'title']);

        return response()->json([
            'success' => true,
            'message' => 'List Destinations',
            'data' => DestinationResource::collection($destinations),
        ]);
    }
}
