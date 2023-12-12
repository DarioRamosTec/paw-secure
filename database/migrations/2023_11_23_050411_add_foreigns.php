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
        Schema::table('pets', function (Blueprint $table) {
            $table->foreign('animal')->references('id')->on('animals');
            $table->foreign('user')->references('id')->on('users');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('sensor')->references('id')->on('sensors');
        });

        Schema::table('spaces', function (Blueprint $table) {
            $table->foreign('user')->references('id')->on('users');
            $table->foreign('target')->references('id')->on('pets');
        });
        
        Schema::table('pet_space', function (Blueprint $table) {
            $table->foreign('space')->references('id')->on('spaces');
            $table->foreign('pet')->references('id')->on('pets');
        });    
        
        Schema::table('sensors', function (Blueprint $table) {
            $table->foreign('sensor_type')->references('id')->on('sensor_type');
            $table->foreign('space')->references('id')->on('spaces');
            $table->foreign('pet')->references('id')->on('pets');
        }); 

        Artisan::call('db:seed', [
            '--class' => 'AnimalSeeder'
        ]);

        Artisan::call('db:seed', [
            '--class' => 'SensorTypeSeeder'
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['animal', 'user']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['sensor']);
        });

        Schema::table('spaces', function (Blueprint $table) {
            $table->dropForeign(['user']);
        });

        Schema::table('pet_space', function (Blueprint $table) {
            $table->dropForeign(['space', 'pet']);
        });    
        
        Schema::table('sensors', function (Blueprint $table) {
            $table->dropForeign(['sensor_type', 'space']);
        }); 
    }
};
