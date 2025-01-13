<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TrackActivity extends Component
{
    #[Locked]
    public Project $project;

    public array $taskForm = [
        'duration' => null,
        'description' => null,
    ];

    public $showSavedState = false;

    protected $rules = [
        'taskForm.description' => 'nullable|string|max:2500',
        'taskForm.duration' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->taskForm = [
            'duration' => config('timetracking.duration'),
            'description' => null,
        ];
    }

    public function saveEntry()
    {
        $this->validate();

        logs()->info('saveEntry', ['task' => $this->taskForm, 'project' => $this->project]);

        // save the task
        if($this->project){
            $this->project->tasks()->create([
                ...$this->taskForm,
                ...['user_id' => auth()->user()->getKey()],
            ]);
        }
        else {
            Task::create([
                ...$this->taskForm,
                ...['user_id' => auth()->user()->getKey()],
            ]);
        }

        $this->showSavedState = true;

        session()->flash('flash.banner', __('Task saved.'));

        $this->dispatch('task.new');

        // prepare a reset model so that the next entry can be saved
        $this->taskForm = [
            'duration' => config('timetracking.duration'),
            'description' => null,
        ];
    }

    public function render()
    {
        return view('livewire.track-activity');
    }
}
