<?php

namespace App\Http\Resources;

use App\Http\Resources\User as UserResouce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
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
                'type' => 'posts',
                'post_id' => $this->id,
                "attributes" => [
                    'posted_by' => new UserResouce($this->user),
                    'body' => $this->body,
                ],
            ],
            "links" => [
                'self' => url('/posts/' . $this->id),
            ],
        ];
    }
}
