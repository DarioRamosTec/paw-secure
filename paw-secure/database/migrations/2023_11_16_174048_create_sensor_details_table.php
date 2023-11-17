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
        Schema::create('sensor_details', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('details');
            $table->integer('measure');
            $table->string('unity');
            $table->unsignedBigInteger('cage_id');
            $table->timestamps();

            $table->foreign('cage_id')->references('id')->on('cages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensor_details');
    }
};
