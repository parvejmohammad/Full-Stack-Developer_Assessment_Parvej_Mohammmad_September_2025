<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDriverRequest;

class DriverController extends Controller
{
    public function index() { return Driver::all(); }

    public function store(StoreDriverRequest $req)
    {
        $driver = Driver::create($req->validated());
        return response()->json($driver, 201);
    }

    public function show(Driver $driver) { return $driver; }

    public function update(StoreDriverRequest $req, Driver $driver)
    {
        $driver->update($req->validated());
        return response()->json($driver);
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return response()->json(['message'=>'deleted']);
    }
}
