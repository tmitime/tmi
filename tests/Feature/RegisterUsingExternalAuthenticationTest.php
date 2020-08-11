<?php

namespace Tests\Feature;

use App\Auth\LoginOrRegisterSocialiteUser;
use App\Identity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Socialite\Two\User;
use Tests\TestCase;
use Illuminate\Support\Str;

class RegisterUsingExternalAuthenticationTest extends TestCase
{
    use RefreshDatabase;
    use LoginOrRegisterSocialiteUser;

    public function test_callback_not_found_for_invalid_provider()
    {
        $response = $this->get(route('connect.callback', ['provider' => 'nonexisting']));

        $response->assertNotFound();
    }

    public function test_user_can_register()
    {
        $token = Str::random(10);

        $serviceUser = (new User())->map([
            'id' => '12345',
            'name' => 'Test',
            'email' => 'test@test.test',
            'avatar' => null,
            'token' => $token,
            'refreshToken' => null,
            'expiresIn' => null,
        ]);

        $user = $this->connect('gitlab', $serviceUser);

        $this->assertTrue($user->wasRecentlyCreated);
        $this->assertEquals('test@test.test', $user->email);
        $this->assertEquals('Test', $user->name);

        $identities = $user->identities;

        $this->assertCount(1, $identities);

        $identity = $identities->first();

        $this->assertEquals('gitlab', $identity->provider);
        $this->assertEquals('12345', $identity->provider_id);
        $this->assertEquals($token, $identity->token);

    }

    public function test_user_can_login()
    {
        $identity = factory(Identity::class)->create([
            'provider' => 'gitlab'
        ]);

        $serviceUser = (new User())->map([
            'id' => $identity->provider_id,
            'name' => 'Test',
            'email' => $identity->user->email,
            'avatar' => null,
            'token' => $identity->token,
            'refreshToken' => null,
            'expiresIn' => null,
        ]);

        $user = $this->connect('gitlab', $serviceUser);

        $this->assertTrue($user->is($identity->user));
        
        $identities = $user->identities;
        
        $this->assertCount(1, $identities);
        $this->assertTrue($identities->first()->is($identity));
    }
}
