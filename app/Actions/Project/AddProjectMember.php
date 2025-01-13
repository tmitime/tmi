<?php

namespace App\Actions\Project;

use App\Models\Member;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class AddProjectMember
{
    /**
     * Add a new project member to the given project.
     *
     * @param  mixed  $user
     * @param  mixed  $project
     * @param  string|null  $role
     * @return void
     */
    public function add($user, $project, string $email, string $role)
    {
        Gate::forUser($user)->authorize('addProjectMember', $project);

        $this->validate($project, $email, $role);

        $newProjectMember = Jetstream::findUserByEmailOrFail($email);

        // AddingProjectMember::dispatch($project, $newProjectMember);

        $project->members()->attach(
            $newProjectMember, ['role' => Member::convertJetstreamRole($role)]
        );

        // ProjectMemberAdded::dispatch($project, $newProjectMember);
    }

    /**
     * Validate the add member operation.
     *
     * @param  mixed  $project
     * @return void
     */
    protected function validate($project, string $email, string $role)
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('We were unable to find a registered user with this email address.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnProject($project, $email)
        )->validateWithBag('addProjectMember');
    }

    /**
     * Get the validation rules for adding a project member.
     *
     * @return array
     */
    protected function rules()
    {
        return array_filter([
            'email' => ['required', 'email', 'exists:users'],
            'role' => ['required', 'string', new Role],
        ]);
    }

    /**
     * Ensure that the user is not already on the project.
     *
     * @param  mixed  $project
     * @return \Closure
     */
    protected function ensureUserIsNotAlreadyOnProject($project, string $email)
    {
        return function ($validator) use ($project, $email) {
            $validator->errors()->addIf(
                $project->hasMemberWithEmail($email),
                'email',
                __('This user has already access as belongs to the team.')
            );
        };
    }
}
