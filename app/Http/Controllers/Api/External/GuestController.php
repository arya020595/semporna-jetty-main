<?php

namespace App\Http\Controllers\Api\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\External\GuestResource;
use App\Models\Guest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:2000',
            'last_synced_at' => 'sometimes|required|date',
            'filter' => 'sometimes|array',
            'filter.last_synced_at' => 'sometimes|required|string|date',
            'sort' => 'sometimes|required|string',
        ]);

        $lastSyncedAt = $validated['filter']['last_synced_at'] ?? $validated['last_synced_at'] ?? null;

        $guests = QueryBuilder::for(Guest::class, $request)
            ->allowedFilters(
                AllowedFilter::callback('last_synced_at', function ($query) use ($lastSyncedAt) {
                    $query->where('updated_at', '>', Carbon::parse($lastSyncedAt)
                        ->setTimezone(config('app.timezone'))->addMinutes(15));
                })->default($lastSyncedAt),
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('ic_no'),
                AllowedFilter::partial('nationality_name'),
                AllowedFilter::exact('gender'),
            )
            ->defaultSort('id')
            ->allowedSorts('id', 'name', 'age')
            ->paginate(
                (int) ($validated['limit'] ?? 1000),
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
