<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize(Project::class);
        
        return view('projects.index', [
            'projects' => Project::with('members')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Project::class);
        
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Project::class);

        $validated = $this->validate($request, [
            'name' => 'required|string|max:250',
            'description' => 'nullable|string|max:3000',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'working_days' => 'nullable|integer|min:1',
        ]);

        $prj = DB::transaction(function() use ($validated, $request){
            
            $project = Project::create($validated);

            $project->members()->attach([
                $request->user()->getKey() => [
                    'role' => Member::ROLE_OWNER
                ]
            ]);

            return $project;

        });

        return redirect()
            ->route('projects.index')
            ->with('flash.banner', __(':project created', ['project' => $prj->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $this->authorize($project);

        $project->load([
            'members',
            'latestTasks'
        ]);

        return view('projects.show', [
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $this->authorize($project);

        return view('projects.edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize($project);

        $validated = $this->validate($request, [
            'name' => 'required|string|max:250',
            'description' => 'nullable|string|max:3000',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'working_days' => 'nullable|integer|min:1',
        ]);

        $project->update($validated);
        
        return redirect()
            ->route('projects.show', $project)
            ->with('flash.banner', __('Project updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $this->authorize($project);

        return redirect()
            ->route('projects.index')
            ->with('flash.banner', __(':project deleted', ['project' => $project->name]));
    }
}
