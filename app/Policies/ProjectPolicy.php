<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Project $project)
    {
        return $project->hasMember($user) ||
               $user->hasTeamPermission($project->team, 'project:read');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role == User::ROLE_MANAGER ||
               $user->hasTeamPermission($user->currentTeam, 'project:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Project $project)
    {
        return $project->hasMember($user, Member::ROLE_OWNER) ||
               $user->hasTeamPermission($project->team, 'project:update');
    }

    /**
     * Determine whether the user can add project members.
     *
     * @return mixed
     */
    public function addProjectMember(User $user, Project $project)
    {
        return $project->hasMember($user, Member::ROLE_OWNER);
    }

    /**
     * Determine whether the user can update project member permissions.
     *
     * @return mixed
     */
    public function updateProjectMember(User $user, Project $project)
    {
        return $project->hasMember($user, Member::ROLE_OWNER);
    }

    /**
     * Determine whether the user can remove project members.
     *
     * @return mixed
     */
    public function removeProjectMember(User $user, Project $project)
    {
        return $project->hasMember($user, Member::ROLE_OWNER);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Project $project)
    {
        return $project->hasMember($user, Member::ROLE_OWNER) ||
               $user->hasTeamPermission($project->team, 'project:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Project $project)
    {
        return $project->hasMember($user, Member::ROLE_OWNER) ||
               $user->hasTeamPermission($project->team, 'project:delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Project $project)
    {
        return $project->hasMember($user, Member::ROLE_OWNER) ||
               $user->hasTeamPermission($project->team, 'project:delete');
    }
}
