<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;


class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;  //refresh database in every test
    /**
     * A basic feature test example.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }
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
    public function test_a_user_can_post_a_text_image_post()
    {

        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->actingAs(
            $user,
            'api'
        );
        $file = UploadedFile::fake()->image('user-post.jpg');
        $response = $this->post('/api/posts', [
            'body' => 'Testing Body',
            'image' => $file,
            'width' => 100,
            'height' => 100,
        ]);
        Storage::disk('public')->assertExists('post-images/' . $file->hashName());
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    "attributes" => [
                        'body' => 'Testing Body',
                        'image' => url('post-images/' . $file->hashName()),
                    ],
                ],
            ]);
    }
}
