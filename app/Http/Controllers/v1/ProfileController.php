<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use App\Http\Resources\UserItemResource;
use App\Models\Item;

class ProfileController extends Controller
{
    /**
     * Get all items created by the authenticated user
     *
     * @authenticated
     *
     * @return JsonResponse Returns a list of items.
     */
    public function items(Request $request)
    {
        $user = $request->user();
        $paginatedItems = $user->items()->latest()->withCount('bids')->paginate(10);
        
        return $this->success([
            'items' => UserItemResource::collection($paginatedItems),
            'pagination' => [
                'current_page' => $paginatedItems->currentPage(),
                'last_page' => $paginatedItems->lastPage(),
                'per_page' => $paginatedItems->perPage(),
                'total' => $paginatedItems->total(),
            ],
        ], 'Items fetched successfully');
    }

    /**
     * Get all items bid on by the authenticated user
     *
     * @authenticated
     *
     * @return JsonResponse Returns a list of items.
     */
    public function bids(Request $request)
    {
        $user = $request->user();

        $paginatedItems = Item::whereHas('bids', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['highestBid'])
        ->withCount('bids')
        ->withMax(['bids as user_bid' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }], 'amount')
        ->latest()
        ->paginate(10);
        
        return $this->success([
            'items' => UserItemResource::collection($paginatedItems),
            'pagination' => [
                'current_page' => $paginatedItems->currentPage(),
                'last_page' => $paginatedItems->lastPage(),
                'per_page' => $paginatedItems->perPage(),
                'total' => $paginatedItems->total(),
            ],
        ], 'Bids fetched successfully');
    }

    /**
     * Get all items in the authenticated user's wishlist
     *
     * @authenticated
     *
     * @return JsonResponse Returns a list of items.
     */
    public function wishlist(Request $request)
    {
        $user = $request->user();

        $paginatedItems = $user->wishlists()->latest()->withCount('bids')->paginate(10);
        
        return $this->success([
            'items' => UserItemResource::collection($paginatedItems),
            'pagination' => [
                'current_page' => $paginatedItems->currentPage(),
                'last_page' => $paginatedItems->lastPage(),
                'per_page' => $paginatedItems->perPage(),
                'total' => $paginatedItems->total(),
            ],
        ], 'Wishlist fetched successfully');
    }
}
