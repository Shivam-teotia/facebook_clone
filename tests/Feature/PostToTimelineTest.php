<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;  //refresh database in every test
    /**
     * A basic feature test example.
     */
    public function test_a_user_can_post_a_text_post()
    {

        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->actingAs(
            $user,
            'api'
        )->post('/api/posts', [
                    'body' => 'Testing Body',
                ]);
        $post = Post::first();
        $this->assertCount(1, Post::all());
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals("Testing Body", $post->body);
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'posts',
                    'post_id' => $post->id,
                    "attributes" => [
                        'posted_by' => [
                            'data' => [
                                'attributes' => [
                                    'name' => $user->name,
                                ],
                            ],
                        ],
                        'body' => 'Testing Body',
                    ],
                ],
                "links" => [
                    'self' => url('/posts/' . $post->id),
                ],
            ]);
    }
}
