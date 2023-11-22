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
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('sensortype');
            $table->unsignedBigInteger('cage');
            $table->timestamps();

            $table->foreign('sensortype')->references('id')->on('sensor_types');
            $table->foreign('cage')->references('id')->on('cages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensors');
    }
};
