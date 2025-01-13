<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
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
    public function view(User $user, Task $task)
    {
        return $task->user_id == $user->getKey()
            || $user->hasTeamPermission($task->project->team, 'task:read')
            || $task->project->hasMember($user, [
                Member::ROLE_MAINTAINER,
                Member::ROLE_COLLABORATOR,
                Member::ROLE_OWNER,
                Member::ROLE_GUEST,
            ]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, ?Project $project = null)
    {
        if (is_null($project)) {
            return true;
        }

        return
            $user->hasTeamPermission($project->team, 'task:create') ||
            $project->hasMember($user, [
                Member::ROLE_MAINTAINER,
                Member::ROLE_COLLABORATOR,
                Member::ROLE_OWNER,
                Member::ROLE_GUEST,
            ]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Task $task)
    {
        return $task->user_id == $user->getKey() ||
            $task->project->hasMember($user, Member::ROLE_OWNER);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Task $task)
    {
        return $task->user_id == $user->getKey() ||
            $task->project->hasMember($user, Member::ROLE_OWNER);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Task $task)
    {
        return $task->user_id == $user->getKey() ||
            $task->project->hasMember($user, Member::ROLE_OWNER);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Task $task)
    {
        return $task->user_id == $user->getKey() ||
            $task->project->hasMember($user, Member::ROLE_OWNER);
    }
}
