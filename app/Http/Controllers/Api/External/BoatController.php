<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\BoatResource;
use App\Models\Boat;
use Illuminate\Http\Request;

class BoatController extends Controller
{
    public function index(Request $request)
    {
        $boats = Boat::query()
            ->orderBy('number')
            ->get(['id', 'company_id', 'number']);

        return response()->json([
            'success' => true,
            'message' => 'List Boats',
            'data' => BoatResource::collection($boats),
        ]);
    }
}
