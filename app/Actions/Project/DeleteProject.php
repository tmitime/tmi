<?php

namespace App\Actions\Project;

class DeleteProject
{
    public function redirectTo()
    {
        return route('projects.index');
    }

    /**
     * Delete the given project.
     *
     * @param  mixed  $project
     * @return void
     */
    public function delete($project)
    {
        $project->purge();
    }
}
