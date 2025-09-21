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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
    $table->unsignedBigInteger('delivery_route_id');
    $table->foreign('delivery_route_id')->references('id')->on('delivery_routes')->onDelete('cascade');
    $table->decimal('value_rs', 12, 2);
    $table->unsignedBigInteger('driver_id')->nullable();
    $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
    
    // Instead of a timestamp, store CSV HH:MM as integer minutes
    $table->integer('delivery_minutes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
