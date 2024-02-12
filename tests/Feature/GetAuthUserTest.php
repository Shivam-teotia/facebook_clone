<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAuthUserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_authenticated_user_can_be_fetched()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $response = $this->get('/api/auth-user');
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
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
}
