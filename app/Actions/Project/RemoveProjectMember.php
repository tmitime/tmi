<?php

namespace App\Actions\Project;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;
use Laravel\Jetstream\Events\TeamMemberRemoved;

class RemoveProjectMember implements RemovesTeamMembers
{
    /**
     * Remove the team member from the given team.
     *
     * @param  mixed  $user
     * @param  mixed  $project
     * @param  mixed  $projectMember
     * @return void
     */
    public function remove($user, $project, $projectMember)
    {
        $this->authorize($user, $project, $projectMember);

        $this->ensureUserDoesNotOwnProject($projectMember, $project);

        // TODO: here another check is required as team members cannot be removed from projects

        $project->removeUser($projectMember);

        // TeamMemberRemoved::dispatch($project, $projectMember);
    }

    /**
     * Authorize that the user can remove the team member.
     *
     * @param  mixed  $user
     * @param  mixed  $project
     * @param  mixed  $projectMember
     * @return void
     */
    protected function authorize($user, $project, $projectMember)
    {
        if (! Gate::forUser($user)->check('removeProjectMember', $project) &&
            $user->id !== $projectMember->id) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the project.
     *
     * @param  mixed  $projectMember
     * @param  mixed  $project
     * @return void
     */
    protected function ensureUserDoesNotOwnProject($projectMember, $project)
    {
        if ($projectMember->id === $project->owner->user_id) {
            throw ValidationException::withMessages([
                'project' => [__('You may not leave a project that you created.')],
            ])->errorBag('removeProjectMember');
        }
    }
}
