<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Models\RefDestination;
use Illuminate\Http\Request;

class DepartureController extends Controller
{
    public function index(Request $request)
    {
        $list = RefDestination::query()
            ->where('title', 'LIKE', "%{$request->search}%")
            ->where('type', RefDestination::TYPE_DEPARTURE)
            ->select('id', 'title as description')
            ->limit(20)
            ->get();

        return response([
            'message' => 'Search Departure',
            'data' => $list
        ], 200);
    }

    public function show(Request $request, RefDestination $departure)
    {
        $departure->description = $departure->title;

        return response([
            'message' => 'Show Departure',
            'data' => $departure
        ], 200);
    }
}
