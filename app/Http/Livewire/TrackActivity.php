<?php

namespace App\Http\Livewire;

use App\Models\Project;
use App\Models\Task;
use Livewire\Component;

class TrackActivity extends Component
{

    public Project $project;

    public Task $task;

    public $showSavedState = false;

    // public $duration = 15;
    
    // public $description;

    protected $rules = [
        'task.description' => 'nullable|string',
        'task.duration' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->task = new Task(['duration' => config('timetracking.duration')]);
    }

    public function saveEntry()
    {
        $this->validate();

        logs()->info('saveEntry', ['task' => $this->task, 'project' => $this->project]);

        // attach the user
        $this->task->user_id = auth()->user()->getKey();

        // save the task
        if($this->project){
            $this->project->tasks()->save($this->task);
        }
        else {
            $this->task->save();
        }

        $this->showSavedState = true;

        session()->flash('flash.banner', __('Task saved.'));

        $this->emit('task.new');

        // prepare a reset model so that the next entry can be saved
        $this->task = new Task(['duration' => 15]);
    }

    public function render()
    {
        return view('livewire.track-activity');
    }
}
