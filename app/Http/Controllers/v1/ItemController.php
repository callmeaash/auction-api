<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use App\Http\Requests\StoreItemRequest;
use App\Category;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;


class ItemController extends Controller
{
    /**
     * Display a listing of the available items.
     *
     * @unauthenticated
     *
     * @return JsonResponse Returns a list of items.
     * 
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        
        $query = Item::with(['user'])
            ->when(
                $request->status === 'ended',
                fn($q) => $q->where('is_active', false),
                fn($q) => $q->where('is_active', true)
            )
            ->when(
                $request->search,
                fn($q, $s) => $q->where('title', 'like', "%$s%")
            )
            ->when(
                $request->category,
                fn($q, $c) => $q->where('category', $c)
            )
            ->when(
                $request->sort === 'highest_bid',
                fn($q) => $q->orderBy('highest_bid', 'desc')
            )
            ->when(
                $request->sort === 'ending_soon',
                fn($q) => $q->orderBy('end_date', 'asc')
            )
            ->when(
                $request->sort === 'newest',
                fn($q) => $q->orderBy('created_at', 'desc')
            )
            ->when(
                $request->sort === 'oldest',
                fn($q) => $q->orderBy('created_at', 'asc')
            )
            ->withCount('bids');

        if($user){
            $query->withExists([
                'wishlistedBy as is_favorited' => fn($q) => $q->where('user_id', $user->id)
            ]);
        }

        $paginated = $query->paginate(10);

        return $this->success([
            'items' => ItemResource::collection($paginated),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ], 'Items fetched successfully');
    }

    /**
     * Create a new listing.
     * 
     * @authenticated
     * 
     * @param StoreItemRequest $request Validated item data.
     * @return JsonResponse Returns the created item.
     */
    public function store(StoreItemRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth('sanctum')->id();

        if($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $validated['end_date'] = now()->addDays((int) $request->duration);
        $item = Item::create($validated);
        return $this->success(new ItemResource($item), 'Item created successfully');
    }

    /**
     * Display the item.
     * 
     * @unauthenticated
     * 
     * @param Item $item The item to display.
     * @return JsonResponse Returns the item.
     */
    public function show(Item $item)
    {
        $user = auth('sanctum')->user();
        $item->load(['user', 'comments.user', 'bids.user']);

        if($user){
            $item->loadExists([
                'wishlistedBy as is_favorited' => fn($q) => $q->where('user_id', $user->id)
            ]);
        }

        return $this->success(new ItemResource($item), 'Item fetched successfully');
    }

    /**
     * Update the item.
     * 
     * @authenticated
     * 
     * @param UpdateItemRequest $request Validated item data.
     * @return JsonResponse Returns the updated item.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        if (Gate::denies('update', $item)) {
            return $this->forbidden('Only the owner can update this item.');
        }

        if ($item->bids()->exists()) {
            return $this->forbidden(
                'Item cannot be updated because it has bids'
            );
        }
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($validated);
        return $this->success(new ItemResource($item), 'Item updated successfully');

    }

    /**
     * Deletes the item
     * 
     * @authenticated
     * 
     * @param Item $item The item to delete.
     * @return JsonResponse Returns a success message.
     */
    public function destroy(Item $item)
    {
        if (Gate::denies('delete', $item)) {
            return $this->forbidden('Only the owner can delete this item.');
        }

        if ($item->bids()->exists()) {
            return $this->forbidden(
                'Item cannot be deleted because it has bids'
            );
        }

        $item->delete();
        return $this->success([], 'Item deleted successfully');
    }

    /**
     * Get all categories
     * 
     * @unauthenticated
     * 
     * @return JsonResponse Returns a list of categories.
     */
    public function categories()
    {
        return $this->success(Category::options(), 'Categories fetched successfully');
    }
}
