<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Driver;
use Carbon\Carbon;

class SimulationService
{
    public function runSimulation(int $availableDrivers, string $routeStartTime, int $maxHoursPerDriver): array
    {
        if ($availableDrivers < 1) {
            throw new \InvalidArgumentException("availableDrivers must be >= 1");
        }

        $orders = Order::with('route')->get();
        $drivers = Driver::take($availableDrivers)->get();

        // Virtual drivers if not enough
        while ($drivers->count() < $availableDrivers) {
            $drivers->push(new Driver([
                'id' => null,
                'name' => 'Virtual',
                'current_shift_hours' => 0,
                'past_7day_hours' => [],
            ]));
        }

        $assignments = [];
        $driverWorkMinutes = array_fill(0, $availableDrivers, 0);

        [$hh, $mm] = explode(':', $routeStartTime);
        $startTime = Carbon::createFromTime($hh, $mm, 0);

        foreach ($orders as $index => $order) {
            $route = $order->route;
            if (!$route) continue;

            // Choose least busy driver
            $driverIdx = array_search(min($driverWorkMinutes), $driverWorkMinutes);
            $driver = $drivers[$driverIdx];

            // Delivery duration from CSV (HH:MM string)
            $durationMinutes = $order->delivery_minutes ?? $route->base_time_minutes;


            // Fatigue Rule
            $speedFactor = ($driver->current_shift_hours > 8) ? 1.3 : 1.0;
            $durationMinutes = (int)ceil($durationMinutes * $speedFactor);

            // Delivery timing
            $assignedStart = (clone $startTime)->addMinutes($driverWorkMinutes[$driverIdx]);
            $eta = (clone $assignedStart)->addMinutes($durationMinutes);

            // Late if ETA > base + 10 mins
            $allowed = (clone $assignedStart)->addMinutes($route->base_time_minutes + 10);
            $isLate = $eta->greaterThan($allowed);

            // Costs & Profit
            $penalty = $isLate ? 50 : 0;
            $bonus = (!$isLate && $order->value_rs > 1000) ? $order->value_rs * 0.1 : 0;

            $perKm = 5 + ($route->traffic_level === 'High' ? 2 : 0);
            $fuelCost = $perKm * $route->distance_km;

            $profit = $order->value_rs + $bonus - $penalty - $fuelCost;

            $assignments[] = [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'driver' => $driver->name,
                'eta' => $eta->toDateTimeString(),
                'is_late' => $isLate,
                'penalty' => $penalty,
                'bonus' => round($bonus, 2),
                'fuel_cost' => round($fuelCost, 2),
                'order_profit' => round($profit, 2),
            ];

            $driverWorkMinutes[$driverIdx] += $durationMinutes;
        }

        // KPIs
        $totalOrders = count($assignments);
        $onTime = count(array_filter($assignments, fn($a) => !$a['is_late']));
        $late = $totalOrders - $onTime;

        return [
            'total_orders' => $totalOrders,
            'on_time_deliveries' => $onTime,
            'late_deliveries' => $late,
            'efficiency_score' => $totalOrders ? round(($onTime / $totalOrders) * 100, 2) : 0,
            'total_profit' => array_sum(array_column($assignments, 'order_profit')),
            'total_fuel_cost' => array_sum(array_column($assignments, 'fuel_cost')),
            'total_penalties' => array_sum(array_column($assignments, 'penalty')),
            'total_bonuses' => array_sum(array_column($assignments, 'bonus')),
            'assignments' => $assignments,
        ];
    }
}
