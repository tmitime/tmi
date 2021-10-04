<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Carbon\Carbon;
use Livewire\Component;

class ProjectTasksStatsMeter extends Component
{
    public Project $project;

    public $stats = [];

    public $working_days = 0;
    
    public $remaining_working_days = 0;
    
    public $colors = [
        'meetings' => 'bg-blue-600',
        'tasks' => 'bg-yellow-500',
    ];

    protected $listeners = ['task.new' => 'refreshStats'];

    public function mount()
    {
        $this->refreshStats();
    }

    public function refreshStats()
    {
        // TODO: optimize

        $total = $this->project->tasks()->count();
        
        $sum = $this->project->tasks()->sum('duration');

        $this->working_days = $sum > 0 ? round( $sum / Carbon::MINUTES_PER_HOUR / config('timetracking.working_day'), 2) : 0;
        
        $this->remaining_working_days = $this->project->working_days ? round( $this->project->working_days - $this->working_days, 2) : null;

        $meetings = $this->project->tasks()->meeting()->count();
        
        $others = $this->project->tasks()->notMeeting()->count();

        $this->stats = [
            'meetings' => $total > 0 ? (100*$meetings) / $total : 0,
            'tasks' => $total > 0 ? (100*$others) / $total : 0,
        ];
    }

    public function render()
    {
        return view('livewire.project-tasks-stats-meter');
    }
}
