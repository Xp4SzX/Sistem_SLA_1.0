<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    public function test_unknown_admin_email_returns_login_error_without_trying_a_username_column(): void
    {
        $guard = Mockery::mock();
        $guard->shouldReceive('check')->andReturnFalse();

        Auth::shouldReceive('guard')->andReturn($guard);
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'midol@gmail.com',
                'password' => 'invalid-password',
            ], false)
            ->andReturnFalse();

        $this->post(route('admin.login'), [
            'username' => 'midol@gmail.com',
            'password' => 'invalid-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('username');
    }
}
