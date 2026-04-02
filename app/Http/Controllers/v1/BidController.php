<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BidResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{

    /**
     * Create a new bid.
     * 
     * @authenticated
     * 
     * @param Request $request Validated bid data.
     * @param Item $item The item to bid on.
     * @return JsonResponse Returns the created bid.
     * 
     */
    public function store(Request $request, Item $item): JsonResponse
    {

        $user = auth('sanctum')->user();
        if($item->user_id === $user->id) {
            return $this->forbidden('User cannot bid for their own items');
        }

        if($item->highestBid) {
            if($item->highestBid->user_id === $user->id) {
                return $this->forbidden('User bid is already highest');
            }
        }

        return DB::transaction(function () use ($request, $item) {
            $item = Item::where('id', $item->id)->lockForUpdate()->first();

            $minBid = $item->highestBid ? ($item->highestBid->amount + 1) : $item->starting_bid;

            $validated = $request->validate([
                'amount' => 'required|numeric|min:' . $minBid,
            ]);

            $bid = $item->bids()->create([
                'user_id' => auth('sanctum')->id(),
                'amount' => $validated['amount'],
            ]);

            $bid->load('user');

            return $this->success(new BidResource($bid), 'Bid created successfully');
        });
    }
}
