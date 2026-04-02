<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    /**
     * Toggles the wishlist status of an item.
     * 
     * @authenticated
     * 
     * @param Item $item The item to toggle.
     * @return JsonResponse Returns a success message.
     */
    public function toggle(Item $item): JsonResponse
    {
        $user = auth('sanctum')->user();
        $status = $user->wishlists()->toggle($item->id);

        $message = count($status['attached']) > 0
            ? 'Item added to wishlist successfully'
            : 'Item removed from wishlist successfully';

        return $this->success([], $message);
    }
}
