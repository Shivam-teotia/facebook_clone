<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCanViewProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_a_user_can_view_user_profiles()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')
            ->get('/api/users/' . $user->id);
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'user_id' => $user->id,
                    'attributes' => [
                        'name' => $user->name,
                    ],
                ],
                'links' => [
                    'self' => url('/users/' . $user->id),
                ],
            ]);
    }
    public function test_a_user_can_fetch_posts_for_a_profile()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($user, 'api')
            ->get('/api/users/' . $user->id . '/posts');
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $post->id,
                            'attributes' => [
                                'body' => $post->first()->body,
                                'posted_at' => $post->first()->created_at->diffForHumans(),
                                'image' => url($post->first()->image),
                                'posted_by' => [
                                    'data' => [
                                        'attributes' => [
                                            'name' => $user->name,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => url('/posts/' . $post->id),
                        ],
                    ],
                ],
            ]);
    }
}
