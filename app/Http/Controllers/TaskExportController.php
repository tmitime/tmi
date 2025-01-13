<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskExportController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $project = $request->has('project') ? Project::findUsingRouteKey($request->input('project')) : null;

        $this->authorize([Task::class, $project]);

        return view('tasks.import', [
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $projectValidated = $this->validate($request, [
            'project' => 'required|uuid|exists:projects,uuid',
        ]);

        $project = $projectValidated['project'] ? Project::findUsingRouteKey($projectValidated['project']) : null;

        // This will verify if the user can view the project
        $this->authorize($project);

        $projectSlug = Str::slug($project->name);
        $stamp = Str::slug(now()->toDateTimeString());

        return response()->streamDownload(function () use ($project) {

            $project->tasks()->orderBy('created_at', 'DESC')->get()->each(function ($t) {
                echo $t->toCsv().PHP_EOL;
            });

        }, "export-{$projectSlug}-{$stamp}.txt");

    }
}
