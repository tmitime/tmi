<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TaskImportController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if a project is already specified
        // we must ensure that the user
        // can create a task inside it

        $projectValidated = $this->validate($request, [
            'project' => 'required|uuid|exists:projects,uuid',
        ]);

        $project = $projectValidated['project'] ? Project::findUsingRouteKey($projectValidated['project']) : null;
        
        $this->authorize([Task::class, $project]);

        $validated = $this->validate($request, [
            'tasks' => 'required|string',
            // 'duration' => 'required|integer|min:1',
            // 'unit' => 'required|integer|min:1',
            // 'date' => $project ? 'required|date|after_or_equal:' . $project->start_at->toDateString() : 'required|date',
            // 'created_at_time' => 'required|date_format:H:i:s', // this should accept strings like 16:40 (hours and minutes) and 16:40:10 (hours, minutes and seconds)
        ]);

        $user_id = $request->user()->getKey();

        $data = collect(Str::of($validated['tasks'])->split('/[\n\r]+/'))->map(function($line){
            
            if(empty($line)){
                return null;
            }

            $parsedLine = str_getcsv($line, ';');
            
            return [
                'date' => $parsedLine[0] ?? null,
                'unit' => $parsedLine[1] ?? null,
                'duration' => (float)$parsedLine[2] ?? null,
                'description' => $parsedLine[3] ?? null,
            ];
        })->filter();

        $validator = Validator::make(
            ['tasks' => $data->toArray()],
            [
                'tasks.*.description' => 'required|string|max:2500',
                'tasks.*.duration' => 'required|numeric|min:0',
                'tasks.*.unit' => 'required|string|in:h,m',
                'tasks.*.date' => 'required|date|after_or_equal:' . $project->start_at->toDateString(),
            ],
            [],
            [
                'tasks.*.description' => 'description',
                'tasks.*.duration' => 'duration',
                'tasks.*.unit' => 'unit',
                'tasks.*.date' => 'date',
            ]
        );
        
        if($validator->fails()){
            $errors = $validator->errors();

            $messages = collect($errors->messages())->groupBy(function($value, $key){
                return Str::between($key, '.', '.');
            })->sortKeys()->mapWithKeys(function($values, $line){

                $msg = __('Line :line contain invalid data', [
                    'line' => $line,
                ]);

                return [$msg => $values->flatten()];
            });

            throw ValidationException::withMessages([
                'tasks' => $messages,
            ]);
        }

        $toCreate = collect($validator->validated()['tasks'])->map(function($d) use($request){
            return [
                'created_at' => Str::contains($d['date'], ':') ? Carbon::parse($d['date']) : Carbon::parse("{$d['date']} 09:00"),
                'duration' => $d['unit'] === 'h' ? $d['duration'] * Carbon::MINUTES_PER_HOUR : $d['duration'],
                'type' => 'tmi:Task',
                'description' => e($d['description']),
                'user_id' => $request->user()->getKey(),
            ];
        });

        $project->tasks()->createMany($toCreate->toArray());

        return redirect()
            ->route('tasks.index', ['project' => $project])
            ->with('flash.banner', __('Tasks imported'));

    }

}
