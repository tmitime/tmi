<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;

class TaskList extends Component
{

    public $tasks;

    public Project $project;

    protected $listeners = ['task.new' => 'refresh'];

    public function mount()
    {
        $this->refresh();
    }

    public function render()
    {
        return view('livewire.task-list');
    }

    public function refresh()
    {
        $this->tasks = optional($this->project)->latestTasks ?? collect();
    }

}
