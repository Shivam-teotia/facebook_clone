<?php

namespace Tests\Feature;

use App\Models\Friend;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class FriendsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_a_user_can_send_a_friend_request(): void
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $response = $this->actingAs($user, 'api')
            ->post('/api/friend-request', [
                'friend_id' => $friend->id,
            ]);
        $response->assertStatus(200);
        $friendRequest = Friend::first();
        $this->assertNotNull($friendRequest);
        $this->assertEquals($friend->id, $friendRequest->friend_id);
        $this->assertEquals($user->id, $friendRequest->user_id);
        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => null,
                ],
            ],
            'links' => [
                'self' => url('/users/' . $friend->id),
            ],
        ]);
    }

    public function test_a_user_can_send_a_friend_request_only_once(): void
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $this->actingAs($user, 'api')
            ->post('/api/friend-request', [
                'friend_id' => $friend->id,
            ])->assertStatus(200);
            $this->actingAs($user, 'api')
            ->post('/api/friend-request', [
                'friend_id' => $friend->id,
            ])->assertStatus(200);
        $friendRequest = Friend::all();
        $this->assertCount(1,$friendRequest);
    }
    public function test_only_valid_users_can_be_friend_requested()
    {
        // $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')
            ->post('/api/friend-request', [
                'friend_id' => 123,
            ]);
        $response->assertStatus(404);
        $friendRequest = Friend::first();
        $this->assertNull($friendRequest);
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'User Not Found',
                'detail' => 'Unable to locate the user with the given information',
            ],
        ]);
    }
    public function test_friend_request_can_be_accepted()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $this->actingAs($user, 'api')
            ->post('/api/friend-request', [
                'friend_id' => $friend->id,
            ])->assertStatus(200);
        $response = $this->actingAs($friend, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $user->id,
                'status' => 1,
            ])->assertStatus(200);
        $friendRequest = Friend::first();
        $this->assertNotNull($friendRequest->confirmed_at);
        $this->assertInstanceOf(Carbon::class, $friendRequest->confirmed_at);
        $this->assertEquals(now()->startOfSecond(), $friendRequest->confirmed_at);
        $this->assertEquals(1, $friendRequest->status);
        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => $friendRequest->confirmed_at->diffForHumans(),
                    'friend_id'=>$friendRequest->friend_id,
                    'user_id'=>$friendRequest->user_id,
                ],
            ],
            'links' => [
                'self' => url('/users/' . $friend->id),
            ],
        ]);
    }

    public function test_friend_request_can_be_ignored()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $this->actingAs($user, 'api')
            ->post('/api/friend-request', [
                'friend_id' => $friend->id,
            ])->assertStatus(200);
        $response = $this->actingAs($friend, 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $user->id,
            ])->assertStatus(204);
        $friendRequest = Friend::first();
        $this->assertNull($friendRequest);
        $response->assertNoContent();
    }

    public function test_only_valid_friend_can_be_accepted()
    {
        $anotherUser = User::factory()->create();
        $response = $this->actingAs($anotherUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => 123,
                'status' => 1,
            ])->assertStatus(404);
        $this->assertNull(Friend::first());
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend with given information',
            ],
        ]);
    }
    public function test_only_a_recepient_can_accept_a_friend_request()
    {
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $thirdUser = User::factory()->create();
        $this->actingAs($user, 'api')
            ->post('/api/friend-request', [
                'friend_id' => $friend->id,
            ])->assertStatus(200);
        $response = $this->actingAs($thirdUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $user->id,
                'status' => 1,
            ])->assertStatus(404);
        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->confirmed_at);
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend with given information',
            ],
        ]);
    }

    public function test_only_a_recepient_can_ignore_a_friend_request()
    {
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $thirdUser = User::factory()->create();
        $this->actingAs($user, 'api')
            ->post('/api/friend-request', [
                'friend_id' => $friend->id,
            ])->assertStatus(200);
        $response = $this->actingAs($thirdUser, 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $user->id,
            ])->assertStatus(404);
        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->confirmed_at);
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend with given information',
            ],
        ]);
    }

    public function test_a_friend_id_is_required_for_friend_requests()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')
            ->post('/api/friend-request', [
                'friend_id' => '',
            ]);
        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('friend_id', $responseArray['errors']['meta']);

    }
    public function test_a_user_id_and_status_is_required_for_friend_request_response()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => '',
                'status' => '',
            ])->assertStatus(422);
        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseArray['errors']['meta']);
        $this->assertArrayHasKey('status', $responseArray['errors']['meta']);
    }

    public function test_a_user_id_is_required_for_ignoring_friend_request()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => '',
            ])->assertStatus(422);
        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseArray['errors']['meta']);
    }
    public function test_a_friendship_is_retrieved_when_fetching_the_profile()
    {
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $confirmedAt = now()->subDay();
        $friendRequest = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,
            'confirmed_at' => $confirmedAt,
            'status' => 1,
        ]);
        $response = $this->actingAs($user, 'api')
            ->get('/api/users/' . $friend->id)
            ->assertStatus(200);

        $response->assertJson([
            'data' => [
                'attributes' => [
                    'friendship' => [
                        'data' => [
                            'friend_request_id' => $friendRequest->id,
                            'attributes' => [
                                'confirmed_at' => '1 day ago',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
    public function test_a_inverse_friendship_is_retrieved_when_fetching_the_profile()
    {
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $confirmedAt = now()->subDay();
        $friendRequest = Friend::create([
            'friend_id' => $user->id,
            'user_id' => $friend->id,
            'confirmed_at' => $confirmedAt,
            'status' => 1,
        ]);
        $response = $this->actingAs($user, 'api')
            ->get('/api/users/' . $friend->id)
            ->assertStatus(200);

        $response->assertJson([
            'data' => [
                'attributes' => [
                    'friendship' => [
                        'data' => [
                            'friend_request_id' => $friendRequest->id,
                            'attributes' => [
                                'confirmed_at' => '1 day ago',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
