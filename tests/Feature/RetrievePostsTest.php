<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RetrievePostsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_a_user_can_retrieve_posts()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $posts = Post::factory(2)->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user, 'api');
        $response = $this->get('/api/posts');
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $posts->last()->id,
                            'attributes' => [
                                'body' => $posts->last()->body,
                            ],
                        ],
                    ],
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $posts->first()->id,
                            'attributes' => [
                                'body' => $posts->first()->body,
                            ],
                        ],
                    ],
                ],
                "links" => [
                    'self' => url('/posts'),
                ],
            ]);
    }
    public function test_a_user_can_only_retrieve_their_post()
    {
        $this->withoutExceptionHandling();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $posts = Post::factory()->create([
            'user_id' => $user1->id,
        ]);
        $response = $this
            ->actingAs($user2, 'api')
            ->get('/api/posts');
        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [],
                'links' => [
                    'self' => url('/posts'),
                ],
            ]);

    }
}