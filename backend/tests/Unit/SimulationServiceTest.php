<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\SimulationService;
use App\Models\DeliveryRoute;
use App\Models\Order;
use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimulationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SimulationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SimulationService();
    }

    public function test_simulation_without_orders_returns_zero_kpis()
    {
        $res = $this->service->runSimulation(2, '09:00', 8);
        $this->assertEquals(0, $res['total_orders']);
        $this->assertEquals(0, $res['total_profit']);
    }

    public function test_simulation_with_one_order_calculates_profit_and_fuel()
    {
        $route = DeliveryRoute::create([
            'route_code' => 'R1',
            'distance_km' => 10,
            'traffic_level' => 'Low',
            'base_time_minutes' => 30,
        ]);

        Order::create([
            'order_code' => 'O1',
            'delivery_route_id' => $route->id,
            'value_rs' => 1200,
            'delivery_minutes' => 30, // from CSV HH:MM → minutes
        ]);

        $res = $this->service->runSimulation(1, '09:00', 8);

        $this->assertEquals(1, $res['total_orders']);
        $this->assertEqualsWithDelta(50.0, $res['total_fuel_cost'], 0.01); // 10 km * 5
        $this->assertEqualsWithDelta(1270.0, $res['total_profit'], 0.1);   // 1200 +120 -50
    }

    public function test_late_penalty_applied_when_eta_exceeds_threshold()
    {
        $route = DeliveryRoute::create([
            'route_code' => 'R2',
            'distance_km' => 5,
            'traffic_level' => 'High',
            'base_time_minutes' => 1,
        ]);

        Order::create([
            'order_code' => 'O2',
            'delivery_route_id' => $route->id,
            'value_rs' => 500,
            'delivery_minutes' => 20, // > base_time+10 (late)
        ]);

        $res = $this->service->runSimulation(1, '09:00', 8);

        $this->assertGreaterThan(0, $res['total_penalties']);
        $this->assertEquals(1, $res['late_deliveries']);
    }

    public function test_driver_fatigue_slows_down_deliveries()
    {
        $route = DeliveryRoute::create([
            'route_code' => 'R3',
            'distance_km' => 5,
            'traffic_level' => 'Low',
            'base_time_minutes' => 20,
        ]);

        Order::create([
            'order_code' => 'O3',
            'delivery_route_id' => $route->id,
            'value_rs' => 500,
            'delivery_minutes' => 20,
        ]);

        Driver::create([
            'name' => 'Fatigued Driver',
            'current_shift_hours' => 9, // triggers fatigue rule
            'past_7day_hours' => [8, 8, 8, 8, 8, 8, 8],
        ]);

        $res = $this->service->runSimulation(1, '09:00', 8);

        $assignment = $res['assignments'][0];
        $this->assertTrue($assignment['order_profit'] < 500); // slower → more fuel/penalty
    }

    public function test_efficiency_score_calculates_correctly()
    {
        $route = DeliveryRoute::create([
            'route_code' => 'R4',
            'distance_km' => 2,
            'traffic_level' => 'Low',
            'base_time_minutes' => 5,
        ]);

        Order::create([
            'order_code' => 'O4',
            'delivery_route_id' => $route->id,
            'value_rs' => 200,
            'delivery_minutes' => 5,
        ]);

        Order::create([
            'order_code' => 'O5',
            'delivery_route_id' => $route->id,
            'value_rs' => 200,
            'delivery_minutes' => 20, // late
        ]);

        $res = $this->service->runSimulation(2, '09:00', 8);

        $this->assertEquals(2, $res['total_orders']);
        $this->assertEquals(1, $res['late_deliveries']);
        $this->assertEquals(50.0, $res['efficiency_score']); // 1 on-time / 2 total
    }
}
