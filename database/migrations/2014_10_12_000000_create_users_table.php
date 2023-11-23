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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('time_verification')->nullable();
            $table->string('middle_name', 40)->nullable();
            $table->string('last_name', 40);
            $table->enum('genre', ['male', 'female', 'other'])->nullable();
            $table->string('password', 256);
            $table->string('country', 40)->nullable();
            //$table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
