<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->string('name', 255);

            $table->timestamps();

            // When the project starts
            $table->dateTime('start_at');

            // When the project ends
            $table->dateTime('end_at')->nullable();

            // Archive the project
            $table->dateTime('archived_at')->nullable();

            // total working days expected
            $table->integer('working_days')->nullable();

            // to allow connecting the project to Gitlab
            // to keep it up to date and retrieve issues/tasks
            $table->json('gitlab')->nullable();

            // if there is a predefined schedule for hours
            // to work on this project during a working week
            $table->json('schedule')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
