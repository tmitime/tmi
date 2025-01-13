<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\CreateTeamForm;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_teams_can_be_created()
    {
        $this->actingAs($user = User::factory(['role' => User::ROLE_MANAGER])->withPersonalTeam()->create());

        Livewire::test(CreateTeamForm::class)
            ->set(['state' => ['name' => 'Test Team']])
            ->call('createTeam');

        $this->assertCount(2, $user->fresh()->ownedTeams);
        $this->assertEquals('Test Team', $user->fresh()->ownedTeams()->latest('id')->first()->name);
    }

    public function test_teams_creation_denied()
    {
        $this->actingAs($user = User::factory(['role' => User::ROLE_USER])->withPersonalTeam()->create());

        Livewire::test(CreateTeamForm::class)
            ->set(['state' => ['name' => 'Test Team']])
            ->call('createTeam');

        $this->assertCount(1, $user->fresh()->ownedTeams);
        $this->assertNotEquals('Test Team', $user->fresh()->ownedTeams()->latest('id')->first()->name);
    }
}
