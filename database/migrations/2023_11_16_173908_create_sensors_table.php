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
            $table->dateTime('time');
            $table->double('measure');
            $table->string('data')->nullable(true);
            $table->boolean('seen')->default(false);
            $table->unsignedBigInteger('sensor_type');
            $table->unsignedBigInteger('space');
            $table->unsignedBigInteger('pet')->nullable();
            $table->timestamps();
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
