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
                    'likes' => new LikeCollection($this->likes),
                    'comments' => new CommentCollection($this->comments),
                    'body' => $this->body,
                    'image' => url($this->image),
                    'posted_at' => $this->created_at->diffForHumans(),
                ],
            ],
            "links" => [
                'self' => url('/posts/' . $this->id),
            ],
        ];
    }
}
