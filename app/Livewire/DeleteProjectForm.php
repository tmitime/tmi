<?php

namespace App\Livewire;

use App\Actions\Project\DeleteProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Jetstream\RedirectsActions;
use Livewire\Component;

class DeleteProjectForm extends Component
{
    use RedirectsActions;

    /**
     * The project instance.
     *
     * @var mixed
     */
    public $project;

    /**
     * Indicates if project deletion is being confirmed.
     *
     * @var bool
     */
    public $confirmingProjectDeletion = false;

    /**
     * Mount the component.
     *
     * @param  mixed  $project
     * @return void
     */
    public function mount($project)
    {
        $this->project = $project;
    }

    /**
     * Delete the project.
     *
     * @param  \App\Actions\Jetstream\DeleteProject  $deleter
     * @return void
     */
    public function deleteProject(DeleteProject $deleter)
    {
        Gate::forUser(Auth::user())->authorize('delete', $this->project);

        $deleter->delete($this->project);

        return $this->redirectPath($deleter);
    }

    public function render()
    {
        return view('livewire.delete-project-form');
    }
}
