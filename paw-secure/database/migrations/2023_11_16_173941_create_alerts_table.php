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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sensor_details');
            $table->string('state');
            $table->string('message');
            $table->unsignedBigInteger('alert_type');
            $table->timestamps();

            $table->foreign('sensor_details')->references('id')->on('sensor_details');
            $table->foreign('alert_type')->references('id')->on('alert_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alerts');
    }
};
