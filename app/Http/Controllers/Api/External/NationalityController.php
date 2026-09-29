<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\NationalityResource;
use App\Models\RefNationality;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class NationalityController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['sort' => 'sometimes|required|string']);

        $nationalities = QueryBuilder::for(RefNationality::class, $request)
            ->defaultSort('title')
            ->allowedSorts('title')
            ->get(['id', 'code', 'title']);

        return response()->json([
            'success' => true,
            'message' => 'List Nationalities',
            'data' => NationalityResource::collection($nationalities),
        ]);
    }
}
