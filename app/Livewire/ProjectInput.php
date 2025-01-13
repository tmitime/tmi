<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class ProjectInput extends Component
{
    public $query = '';

    public array $projects = [];

    public ?string $selectedProject;

    public int $highlightIndex = 0;

    public bool $showDropdown = false;

    public function mount()
    {

        // TODO: if there is a selection I need to ensure that everything is set
        // $this->query = $project['name'];
        //     $this->selectedProject = $project['uuid'];

        if ($this->selectedProject) {

            $prj = Project::findUsingRouteKey($this->selectedProject);

            if ($prj) {
                $this->projects = [$prj];
                $this->highlightIndex = 0;
                $this->query = $prj->name;
                // $this->selectedProject = null;
                $this->showDropdown = false;
            }
        } else {
            $this->projects = [];
            $this->highlightIndex = 0;
            $this->query = '';
            $this->selectedProject = null;
            $this->showDropdown = false;
        }

        logs()->warning($this->selectedProject);
    }

    public function reset(...$properties)
    {
        $this->projects = [];
        $this->highlightIndex = 0;
        $this->query = '';
        $this->selectedProject = null;
        $this->showDropdown = false;
    }

    public function hideDropdown()
    {
        $this->showDropdown = false;
    }

    public function incrementHighlight()
    {
        if ($this->highlightIndex === count($this->projects) - 1) {
            $this->highlightIndex = 0;

            return;
        }

        $this->highlightIndex++;
    }

    public function decrementHighlight()
    {
        if ($this->highlightIndex === 0) {
            $this->highlightIndex = count($this->projects) - 1;

            return;
        }

        $this->highlightIndex--;
    }

    public function selectProject($id = null)
    {
        $id = $id ?: $this->highlightIndex;

        $project = $this->projects[$id] ?? null;

        if ($project) {
            $this->highlightIndex = $id;
            $this->showDropdown = true;
            $this->query = $project['name'];
            $this->selectedProject = $project['uuid'];
        }
    }

    public function fetchAutocomplete()
    {
        // TODO: define query scopes for ordering by latest updated/latest added task

        /** @var \App\Models\User */
        $user = auth()->user();

        $this->projects = Project::take(5)
            ->when($this->query, function ($query, $term) {
                return $query->where('name', 'like', '%'.$term.'%');
            })
            ->withMember($user)
            ->get()
            ->toArray();
        $this->showDropdown = true;
    }

    public function updatedQuery()
    {
        $this->fetchAutocomplete();
    }

    public function render()
    {
        return view('livewire.project-input');
    }
}
