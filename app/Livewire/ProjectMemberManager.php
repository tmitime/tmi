<?php

namespace App\Livewire;

use App\Actions\Project\AddProjectMember;
use App\Actions\Project\RemoveProjectMember;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Role;
use Livewire\Component;

class ProjectMemberManager extends Component
{
    /**
     * The project instance.
     *
     * @var mixed
     */
    public $project;

    /**
     * Indicates if a user's role is currently being managed.
     *
     * @var bool
     */
    public $currentlyManagingRole = false;

    /**
     * The user that is having their role managed.
     *
     * @var mixed
     */
    public $managingRoleFor;

    /**
     * The current role for the user that is having their role managed.
     *
     * @var string
     */
    public $currentRole;

    /**
     * Indicates if the application is confirming if a user wishes to leave the current project.
     *
     * @var bool
     */
    public $confirmingLeavingProject = false;

    /**
     * Indicates if the application is confirming if a project member should be removed.
     *
     * @var bool
     */
    public $confirmingProjectMemberRemoval = false;

    /**
     * The ID of the project member being removed.
     *
     * @var int|null
     */
    public $projectMemberIdBeingRemoved = null;

    /**
     * The "add project member" form state.
     *
     * @var array
     */
    public $addProjectMemberForm = [
        'email' => '',
        'role' => null,
    ];

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
     * Add a new project member to a project.
     *
     * @return void
     */
    public function addProjectMember()
    {
        $this->resetErrorBag();

        app()->make(AddProjectMember::class)->add(
            $this->user,
            $this->project,
            $this->addProjectMemberForm['email'],
            $this->addProjectMemberForm['role']
        );

        $this->addProjectMemberForm = [
            'email' => '',
            'role' => null,
        ];

        $this->project = $this->project->fresh();

        $this->dispatch('saved');
    }

    /**
     * Remove the currently authenticated user from the project.
     *
     * @param  \App\Actions\Project\RemoveProjectMember  $remover
     * @return void
     */
    public function leaveProject(RemoveProjectMember $remover)
    {
        $remover->remove(
            $this->user,
            $this->project,
            $this->user
        );

        $this->confirmingLeavingProject = false;

        $this->project = $this->project->fresh();

        return redirect(config('fortify.home'));
    }

    /**
     * Confirm that the given project member should be removed.
     *
     * @param  int  $userId
     * @return void
     */
    public function confirmProjectMemberRemoval($userId)
    {
        $this->confirmingProjectMemberRemoval = true;

        $this->projectMemberIdBeingRemoved = $userId;
    }

    /**
     * Remove a project member from the project.
     *
     * @param  \App\Actions\Project\RemoveProjectMember  $remover
     * @return void
     */
    public function removeProjectMember(RemoveProjectMember $remover)
    {
        $remover->remove(
            $this->user,
            $this->project,
            $user = Jetstream::findUserByIdOrFail($this->projectMemberIdBeingRemoved)
        );

        $this->confirmingProjectMemberRemoval = false;

        $this->projectMemberIdBeingRemoved = null;

        $this->project = $this->project->fresh();
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /**
     * Get the available project member roles.
     *
     * @return array
     */
    public function getRolesProperty()
    {
        return collect(Jetstream::$roles)->except('owner')->transform(function ($role) {
            return with($role->jsonSerialize(), function ($data) {
                return (new Role(
                    $data['key'],
                    $data['name'],
                    $data['permissions']
                ))->description($data['description']);
            });
        })->values()->all();
    }

    public function render()
    {
        return view('livewire.project-member-manager');
    }
}
