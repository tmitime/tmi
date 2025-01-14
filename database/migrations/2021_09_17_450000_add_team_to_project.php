<?php

use App\Models\Project;
use App\Models\Team;
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
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignIdFor(Team::class)->nullable()->after('id');
        });

        // By default I'm attaching all created projects to the
        // personal team of the owner. This works as currently
        // a project can have only one owner

        Project::with('owner')->chunk(50, function ($projects) {
            $projects->each(function ($project) {
                if ($personalTeam = $project->owner->personalTeam()) {
                    $project->team_id = $personalTeam->getKey();
                    $project->save();
                }
            });
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('team_id');
        });
    }
};
