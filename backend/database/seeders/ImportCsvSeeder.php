<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\delivery_route;
use App\Models\Order;
use League\Csv\Reader;

class ImportCsvSeeder extends Seeder
{
    public function run()
    {
        $basePath = database_path('seeders/data');

        // --- Import Drivers ---
        $driverCsv = Reader::createFromPath($basePath.'/drivers.csv', 'r');
        $driverCsv->setHeaderOffset(0);
        foreach ($driverCsv as $row) {
            Driver::create([
                'name' => $row['name'],
                'current_shift_hours' => $row['shift_hours'] ?? 0,
                'past_7day_hours' => explode('|', $row['past_week_hours']),
            ]);
        }

        // --- Import Routes ---
        $routeCsv = Reader::createFromPath($basePath.'/routes.csv', 'r');
        $routeCsv->setHeaderOffset(0);
        foreach ($routeCsv as $row) {
            delivery_route::create([
                'route_code' => $row['route_id'],
                'distance_km' => $row['distance_km'],
                'traffic_level' => $row['traffic_level'],
                'base_time_minutes' => $row['base_time_min'],
            ]);
        }

        // --- Import Orders ---
        $orderCsv = Reader::createFromPath($basePath.'/orders.csv', 'r');
        $orderCsv->setHeaderOffset(0);
        foreach ($orderCsv as $row) {
        // Convert HH:MM string → total minutes
        [$h, $m] = explode(':', $row['delivery_time']);
        $minutes = ((int)$h * 60) + (int)$m;

        Order::create([
            'order_code' => $row['order_id'],
            'delivery_route_id' => DeliveryRoute::where('route_code', $row['route_id'])->first()->id ?? null,
            'value_rs' => $row['value_rs'],
            'delivery_minutes' => $minutes,
        ]);
      }
    }
}
