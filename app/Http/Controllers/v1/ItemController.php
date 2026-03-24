<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use App\Http\Requests\StoreItemRequest;
use App\Category;

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
        
        $query = Item::with('user')
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
            );

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
     * Display an item.
     * 
     * @unauthenticated
     * 
     * @param Item $item The item to display.
     * @return JsonResponse Returns the item.
     */
    public function show(Item $item)
    {
        $user = auth('sanctum')->user();
        $item->load('user');

        if($user){
            $item->loadExists([
                'wishlistedBy as is_favorited' => fn($q) => $q->where('user_id', $user->id)
            ]);
        }

        return $this->success(new ItemResource($item), 'Item fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
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
