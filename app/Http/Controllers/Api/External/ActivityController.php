<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\ActivityResource;
use App\Models\RefActivity;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['sort' => 'sometimes|required|string']);

        $activities = QueryBuilder::for(RefActivity::class, $request)
            ->defaultSort('title')
            ->allowedSorts('title')
            ->get(['id', 'code', 'title']);

        return response()->json([
            'success' => true,
            'message' => 'List Activities',
            'data' => ActivityResource::collection($activities),
        ]);
    }
}
