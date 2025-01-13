<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'project:create',
            'project:read',
            'project:update',
            'project:delete',
            'task:create',
            'task:read',
            'task:update',
            'task:delete',
            'task:import',
            'task:export',
        ]);

        // Jetstream::role('owner', 'Owner', [
        //     'project:create',
        //     'project:read',
        //     'project:update',
        //     'project:delete',
        //     'task:create',
        //     'task:read',
        //     'task:update',
        //     'task:delete',
        //     'task:import',
        //     'task:export',
        // ])->description('Owner.');

        Jetstream::role('admin', 'Maintainer', [
            'project:read',
            'project:create',
            'project:update',
            'task:read',
            'task:create',
            'task:update',
            'task:import',
        ])->description('Help you with management activities.');

        Jetstream::role('collaborator', 'Collaborator', [
            'project:read',
            'task:read',
            'task:create',
            'task:update',
            'task:import',
        ])->description('A member that can track time and see what\'s going on.');

        Jetstream::role('guest', 'Guest', [
            'project:read',
            'task:read',
            'task:create',
            'task:update',
            'task:import',
        ])->description('External users that can see what\'s happening and track only their time.');

        Jetstream::role('observer', 'Observer', [
            'project:read',
        ])->description('An external user that can view general reports, e.g. a client that want to see a monthly report.');
    }
}
