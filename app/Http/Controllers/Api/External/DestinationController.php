<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\DestinationResource;
use App\Models\RefDestination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $destinations = RefDestination::query()
            ->where('type', RefDestination::TYPE_DESTINATION)
            ->orderBy('title')
            ->get(['id', 'code', 'title']);

        return response()->json([
            'success' => true,
            'message' => 'List Destinations',
            'data' => DestinationResource::collection($destinations),
        ]);
    }
}
