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
        Schema::create('pet_cages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cage_id');
            $table->unsignedBigInteger('pet_id');
            $table->timestamps();

            $table->foreign('cage_id')->references('id')->on('cages');
            $table->foreign('pet_id')->references('id')->on('pets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_cages');
    }
};
