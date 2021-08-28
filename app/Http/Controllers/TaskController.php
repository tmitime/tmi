<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Task::class);


        $filters = [
            'user' => $request->user(),
            'project' => $request->has('project') ? Project::findUsingRouteKey($request->input('project')) : null,
        ];

        $tasks = Task::where('user_id', $request->user()->getKey())
            ->orderBy('created_at', 'DESC')
            ->when($filters['project'], function($query, $prj){
                return $query->where('project_id', $prj->getKey());
            })
            ->with('project')
            ->get();

        return view('tasks.index', [
            'tasks' => $tasks,
            'filters' => $filters,
            'hasFilters' => collect($filters)->values()->filter()->isNotEmpty(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $project = $request->has('project') ? Project::findUsingRouteKey($request->input('project')) : null;

        $this->authorize([Task::class, $project]);

        return view('tasks.create', [
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if a project is already specified
        // we must ensure that the user
        // can create a task inside it

        $projectValidated = $this->validate($request, [
            'project' => 'nullable|uuid|exists:projects,uuid',
        ]);

        $project = $projectValidated['project'] ? Project::findUsingRouteKey($projectValidated['project']) : null;
        
        $this->authorize([Task::class, $project]);

        $validated = $this->validate($request, [
            'description' => 'required|string|max:250',
            'duration' => 'required|integer|min:1',
            'created_at_date' => $project ? 'required|date|after_or_equal:' . $project->start_at->toDateString() : 'required|date',
            'created_at_time' => 'required|date_format:H:i:s', // this should accept strings like 16:40 (hours and minutes) and 16:40:10 (hours, minutes and seconds)
            'type' => 'required|string|max:250|in:tmi:Task,tmi:Meeting',
        ]);

        $task = new Task(Arr::only($validated, ['duration', 'type', 'description']));

        $creation_date = Carbon::parse("{$validated['created_at_date']} {$validated['created_at_time']}");
        $task->created_at = $creation_date;

        $task->user_id = $request->user()->getKey();

        if($project){
            $project->tasks()->save($task);
        }
        else {
            $task->save();
        }



        return redirect()
            ->route('tasks.index', $project ? ['project' => $project] : [])
            ->with('flash.banner', __('Task added'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $this->authorize($task);

        return view('tasks.show', [
            'project' => $task->project,
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $this->authorize($task);

        // since we can arrive to the edit page from
        // different locations, we store the
        // arrival path to redirect back
        redirect()->setIntendedUrl(url()->previous());

        return view('tasks.edit', [
            'project' => $task->project,
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize($task);

        $validated = $this->validate($request, [
            'description' => 'required|string|max:250',
            'duration' => 'required|integer|min:1',
            'created_at_date' => 'required|date|after_or_equal:' . $task->project->start_at->toDateString(),
            'created_at_time' => 'required|date_format:H:i:s', // this should accept strings like 16:40 (hours and minutes) and 16:40:10 (hours, minutes and seconds)
            'type' => 'required|string|max:250|in:tmi:Task,tmi:Meeting',
        ]);
        
        $updated_creation_date = Carbon::parse("{$validated['created_at_date']} {$validated['created_at_time']}");
        
        $task->fill(Arr::only($validated, ['duration', 'type', 'description']));
        
        if(abs($task->created_at->diffInSeconds($updated_creation_date)) > Carbon::SECONDS_PER_MINUTE / 2){
            // Only changing the creation time if the edit is above 30 seconds
            $task->created_at = $updated_creation_date;
        }
        
        $task->save();

        return redirect()
            ->intended(route('tasks.index', ['project' => $task->project]))
            ->with('flash.banner', __('Task updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $this->authorize($task);

        $task->delete();

        return redirect()
            ->route('projects.show', $task->project)
            ->with('flash.banner', __('Task deleted'));
    }
}
