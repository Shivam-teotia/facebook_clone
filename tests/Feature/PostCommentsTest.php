<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostCommentsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_a_user_can_comment_on_a_post()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'id' => 123,
        ]);
        $response = $this->actingAs($user, 'api')
            ->post('/api/posts/' . $post->id . '/comment', [
                'body' => 'A great comment here',
            ]);
        $response->assertStatus(200);
        $comment = Comment::first();
        $this->assertCount(1, Comment::all());
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
        $this->assertEquals('A great comment here', $comment->body);
        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'comments',
                        'comment_id' => 1,
                        'attributes' => [
                            'commented_by' => [
                                'data' => [
                                    'user_id' => $user->id,
                                    'attributes' => [
                                        'name' => $user->name,
                                    ],
                                ],
                            ],
                            'body' => 'A great comment here',
                            'commented_at' => $comment->created_at->diffForHumans(),
                        ],
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
    public function test_a_body_is_required_to_leave_a_comment_on_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'id' => 123,
        ]);
        $response = $this->actingAs($user, 'api')
            ->post('/api/posts/' . $post->id . '/comment', [
                'body' => '',
            ])->assertStatus(422);
        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('body', $responseArray['errors']['meta']);
    }
    public function test_posts_are_returned_with_comments()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'id' => 123,
        ]);
        $this->actingAs($user, 'api')
            ->post('/api/posts/' . $post->id . '/comment', [
                'body' => 'A great comment here',
            ]);
        $response = $this->get('/api/posts');
        $comment = Comment::first();
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'attributes' => [
                                'comments' => [
                                    'data' => [
                                        [
                                            'data' => [
                                                'type' => 'comments',
                                                'comment_id' => 1,
                                                'attributes' => [
                                                    'commented_by' => [
                                                        'data' => [
                                                            'user_id' => $user->id,
                                                            'attributes' => [
                                                                'name' => $user->name,
                                                            ],
                                                        ],
                                                    ],
                                                    'body' => 'A great comment here',
                                                    'commented_at' => $comment->created_at->diffForHumans(),
                                                ],
                                            ],
                                            'likes' => [
                                                'self' => url('/posts/123'),
                                            ],
                                        ],
                                    ],
                                    'comment_count' => 1,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }
}
