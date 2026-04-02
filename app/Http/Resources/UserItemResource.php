<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserItemResource extends JsonResource
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
            'starting_bid' => $this->starting_bid,
            'current_bid'  => $this->highestBid ? $this->highestBid->amount : null,
            'total_bids'   => $this->bids_count,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'user_bid' =>$this->whenHas('user_bid', fn() => $this->user_bid),
        ];
    }
}
