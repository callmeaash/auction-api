<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'fullname' => $this->fullname,
            'avatar' => $this->avatar,
            'country' => $this->country,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
