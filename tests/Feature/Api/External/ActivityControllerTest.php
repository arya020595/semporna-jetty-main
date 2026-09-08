<?php

namespace Tests\Feature\Api\External;

use App\Models\RefActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    private function validToken(): string
    {
        return config('external_api.token');
    }

    public function test_valid_token_returns_active_activities_only()
    {
        $activity = RefActivity::factory()->create();
        RefActivity::factory()->create()->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->validToken())
            ->getJson('/api/external/activities');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('data.0.id', $activity->id)
            ->assertJsonCount(1, 'data');
    }

    public function test_missing_token_is_unauthorized()
    {
        $response = $this->getJson('/api/external/activities');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'data' => null,
            ]);
    }

    public function test_wrong_token_is_unauthorized()
    {
        $response = $this->withHeader('Authorization', 'Bearer wrong-token')
            ->getJson('/api/external/activities');

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }
}
