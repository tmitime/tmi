<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RedirectToExternalAuthenticationTest extends TestCase
{
    public function test_redirect_not_performed_if_provider_do_not_exist()
    {
        $response = $this->get(route('connect.provider', ['provider' => 'nonexisting']));

        $response->assertNotFound();
    }

    public function test_redirect_to_gitlab()
    {
        config([
            'services.gitlab.client_id' => 'client',
            'services.gitlab.client_secret' => 'secret',
        ]);

        $response = $this->get(route('connect.provider', ['provider' => 'gitlab']));

        $response->assertRedirect();

        $location = $response->headers->get('Location');

        $this->assertStringContainsString('openid+read_api', $location);
        $this->assertStringContainsString('client_id=client', $location);
        $this->assertStringContainsString('redirect_uri=' . urlencode(route('connect.callback', ['provider' => 'gitlab'])), $location);
    }
}
