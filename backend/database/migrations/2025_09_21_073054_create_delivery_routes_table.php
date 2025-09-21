<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id();
             $table->string('route_code')->unique(); // route ID/reference
            $table->float('distance_km');
            $table->enum('traffic_level', ['Low','Medium','High'])->default('Medium');
            // base_time in minutes required for route (integer)
            $table->integer('base_time_minutes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_routes');
    }
};
