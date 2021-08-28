<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Carbon\Carbon;
use Livewire\Component;

class ProjectSummary extends Component
{

    public Project $project;

    // attributes to get inputs and configuration externally
    public $start;
    
    public $end;
    
    public $period;
    
    // properties to be used
    public $start_date;
    
    public $end_date;
    
    public $entries;

    protected $listeners = ['task.new' => 'refresh'];

    public function mount()
    {

        // if period or start+end not defined we use the current week
        $today = today()->toImmutable();

        list($weekStartAt, $weekEndAt) = config('timetracking.working_week');
        $this->start_date = new Carbon($today->startOfWeek($weekStartAt));
        $this->end_date = new Carbon($today->endOfWeek());
        
        $this->refresh();
    }

    public function refresh()
    {
        $this->entries = $this->project->tasks()
            ->period($this->start_date, $this->end_date)
            ->selectRaw('date_format(created_at, "%w") as step, date_format(created_at, "%M-%D") as day, COUNT(*) AS tasks, SUM(duration) as time')
            ->groupBy(['step', 'day'])
            ->orderBy('day', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.project-summary');
    }
}
