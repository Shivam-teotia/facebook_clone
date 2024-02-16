<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\User as UserResouce;
use Illuminate\Http\Resources\Json\JsonResource;

class Comment extends JsonResource
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
                'type' => 'comments',
                'comment_id' => $this->id,
                'attributes' => [
                    'commented_by' => new UserResouce($this->user),
                    'body' => $this->body,
                    'commented_at' => $this->created_at->diffForHumans(),
                ],
            ],
            'likes' => [
                'self' => url('/posts/' . $this->post_id),
            ],
        ];
    }
}
