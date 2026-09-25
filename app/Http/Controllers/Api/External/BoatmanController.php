<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\BoatmanResource;
use App\Models\Boatman;
use Illuminate\Http\Request;

class BoatmanController extends Controller
{
    public function index(Request $request)
    {
        $boatmen = Boatman::query()
            ->orderBy('name')
            ->get(['id', 'boat_id', 'company_id', 'name', 'ic_no', 'type']);

        return response()->json([
            'success' => true,
            'message' => 'List Boatmen',
            'data' => BoatmanResource::collection($boatmen),
        ]);
    }
}
