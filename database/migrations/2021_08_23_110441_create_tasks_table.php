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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->timestamps();

            $table->foreignId('user_id');

            $table->foreignId('project_id')->nullable(); // possibility to connect a task to a project later

            // Duration of the task in minutes
            $table->integer('duration')->default(1);

            $table->mediumText('description')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
