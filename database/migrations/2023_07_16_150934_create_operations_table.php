<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('route_id');
            $table->unsignedBigInteger('bus_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('numbers_of_seats');
            $table->dateTime('departure');
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('route_id')->references('id')->on('routes');
            $table->foreign('bus_id')->references('id')->on('buses');
            $table->foreign('driver_id')->references('id')->on('drivers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
};
