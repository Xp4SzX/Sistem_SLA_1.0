<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_displays_department_name_without_loading_departments_table(): void
    {
        $user = $this->createUser([
            'department_name' => 'Dinas Kesehatan',
        ]);

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Dinas Kesehatan');
    }

    public function test_profile_update_saves_department_name_directly_on_user(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Pelapor Baru',
                'email' => $user->email,
                'whatsapp_number' => '08123456789',
                'department_name' => 'Dinas Pendidikan',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHas('success');

        $this->assertSame('Dinas Pendidikan', $user->fresh()->department_name);
    }

    private function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'identity_number' => 'PROFILE-TEST-001',
            'name' => 'Pelapor Test',
            'email' => 'profile-test@example.test',
            'whatsapp_number' => '08123456789',
            'password' => 'password',
            'role' => 'user',
        ], $attributes));
    }
}
