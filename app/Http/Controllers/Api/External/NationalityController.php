<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\NationalityResource;
use App\Models\RefNationality;
use Illuminate\Http\Request;

class NationalityController extends Controller
{
    public function index(Request $request)
    {
        $nationalities = RefNationality::query()
            ->orderBy('title')
            ->get(['id', 'code', 'title']);

        return response()->json([
            'success' => true,
            'message' => 'List Nationalities',
            'data' => NationalityResource::collection($nationalities),
        ]);
    }
}
