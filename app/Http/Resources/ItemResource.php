<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

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
            'image' => $this->image,
            'starting_bid' => $this->starting_bid,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'is_active' => $this->is_active,
            'user' => new UserResource($this->whenLoaded('user')),
            'is_favorited' => $this->whenHas('is_favorited', fn() => (bool) $this->is_favorited),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
