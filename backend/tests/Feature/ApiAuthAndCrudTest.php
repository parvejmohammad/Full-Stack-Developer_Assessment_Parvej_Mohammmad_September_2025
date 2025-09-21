<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DeliveryRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiAuthAndCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login_and_access_protected_routes()
    {
        $resp = $this->postJson('/api/register', [
            'name'=>'Manager',
            'email'=>'manager@example.com',
            'password'=>'secret123',
            'password_confirmation'=>'secret123'
        ]);
        $resp->assertStatus(201);
        $token = $resp->json('token');
        $this->assertNotEmpty($token);

        $headers = ['Authorization' => "Bearer {$token}"];

        // create a route
        $create = $this->postJson('/api/routes', [
            'route_code'=>'R100','distance_km'=>10,'traffic_level'=>'Low','base_time_minutes'=>30
        ], $headers);

        $create->assertStatus(201);
    }
}
