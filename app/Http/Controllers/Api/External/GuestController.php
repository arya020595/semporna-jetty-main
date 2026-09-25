<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\GuestResource;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:500',
        ]);

        $guests = Guest::query()
            ->orderBy('id')
            ->paginate(
                (int) ($validated['limit'] ?? 500),
                ['id', 'name', 'ic_no', 'nationality_name', 'age', 'gender'],
                'page',
                (int) ($validated['page'] ?? 1)
            );

        return response()->json([
            'success' => true,
            'message' => 'List Guests',
            'data' => GuestResource::collection($guests->getCollection()),
            'meta' => [
                'current_page' => $guests->currentPage(),
                'per_page' => $guests->perPage(),
                'total' => $guests->total(),
                'last_page' => $guests->lastPage(),
            ],
        ]);
    }
}
