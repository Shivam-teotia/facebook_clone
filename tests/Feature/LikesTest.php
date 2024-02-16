<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_a_user_can_like_a_post()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'id' => 123,
        ]);
        $response = $this->actingAs($user, 'api')
            ->post('/api/posts/' . $post->id . '/likes');
        $response->assertStatus(200);
        $this->assertCount(1, $user->likedPosts);
        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'likes',
                        'like_id' => 1,
                        'attributes' => [],
                    ],
                    'likes' => [
                        'self' => url('/posts/123'),
                    ],
                ],
            ],
            'links' => [
                'self' => url('/posts'),
            ],
        ]);
    }
    public function test_posts_are_returned_with_likes()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'id' => 123,
        ]);
        $this->actingAs($user, 'api')
            ->post('/api/posts/' . $post->id . '/likes')
            ->assertStatus(200);
        $response = $this->get('/api/posts')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'attributes' => [
                                'likes' => [
                                    'data' => [
                                        [
                                            'data' => [
                                                'type' => "likes",
                                                'like_id' => 1,
                                                'attributes' => [],
                                            ],
                                        ],
                                    ],
                                    'like_count' => 1,
                                    'user_likes_post' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }
}
