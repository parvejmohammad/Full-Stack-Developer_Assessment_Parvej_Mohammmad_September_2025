<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRouteRequest;

class DeliveryRouteController extends Controller
{
    public function index() { return DeliveryRoute::all(); }

    public function store(StoreRouteRequest $req)
    {
        $route = DeliveryRoute::create($req->validated());
        return response()->json($route, 201);
    }

    public function show(DeliveryRoute $deliveryRoute) { return $deliveryRoute; }

    public function update(StoreRouteRequest $req, DeliveryRoute $deliveryRoute)
    {
        $deliveryRoute->update($req->validated());
        return response()->json($deliveryRoute);
    }

    public function destroy(DeliveryRoute $deliveryRoute)
    {
        $deliveryRoute->delete();
        return response()->json(['message'=>'deleted']);
    }
}
