<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SimulationService;
use App\Models\Simulation;

class SimulationController extends Controller
{
    protected $service;
    public function __construct(SimulationService $service)
    {
        $this->service = $service;
    }

    // run simulation
    public function run(Request $request)
    {
        $request->validate([
            'available_drivers' => 'required|integer|min:1',
            'route_start_time' => ['required','regex:/^\d{2}:\d{2}$/'], // HH:MM
            'max_hours_per_driver' => 'required|integer|min:1'
        ]);

        $availableDrivers = (int)$request->input('available_drivers');
        $routeStartTime = $request->input('route_start_time'); // HH:MM
        $maxHours = (int)$request->input('max_hours_per_driver');

        // call service
        $result = $this->service->runSimulation($availableDrivers, $routeStartTime, $maxHours);

        // persist
        $sim = Simulation::create([
            'available_drivers' => $availableDrivers,
            'route_start_time' => $routeStartTime . ':00',
            'max_hours_per_driver' => $maxHours,
            'inputs' => ['available_drivers'=>$availableDrivers,'route_start_time'=>$routeStartTime,'max_hours_per_driver'=>$maxHours],
            'results' => $result,
        ]);

        return response()->json(['simulation'=>$sim, 'results'=>$result]);
    }

    public function history()
    {
        return Simulation::orderBy('created_at','desc')->limit(50)->get();
    }

    public function show(Simulation $simulation)
    {
        return $simulation;
    }
}
