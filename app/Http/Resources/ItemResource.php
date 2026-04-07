<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BidResource;
use App\Http\Resources\CommentResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'image_url' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'starting_bid' => (double) $this->starting_bid,
            'current_bid' => $this->highestBid ? (double) $this->highestBid->amount : null,
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'is_active' => (bool) $this->is_active,
            'total_bids' => (int) $this->bids_count,
            'user' => new UserResource($this->whenLoaded('user')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'bids' => BidResource::collection($this->whenLoaded('bids')),
            'is_favorited' => $this->whenHas('is_favorited', fn() => (bool) $this->is_favorited),
        ];
    }
}
