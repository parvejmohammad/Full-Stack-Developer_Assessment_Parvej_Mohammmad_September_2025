<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOrderRequest;

class OrderController extends Controller
{
    public function index() { return Order::with('route','driver')->get(); }

    public function store(StoreOrderRequest $req)
    {
        $order = Order::create($req->validated());
        return response()->json($order, 201);
    }

    public function show(Order $order)
    {
        return $order->load('route','driver');
    }

    public function update(StoreOrderRequest $req, Order $order)
    {
        $order->update($req->validated());
        return response()->json($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message'=>'deleted']);
    }
}
