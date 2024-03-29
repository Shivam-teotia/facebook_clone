<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Like extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'type' => 'likes',
                'like_id' => $this->id,
                'attributes' => [],
            ],
            'likes' => [
                'self' => url('/posts/' . $this->pivot->post_id),
            ],
        ];
    }
}
