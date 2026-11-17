<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTicketingDepartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ticket_list_shows_the_department_that_owns_the_ticket_service(): void
    {
        $admin = User::create([
            'identity_number' => 'TICKET-DEPT-ADMIN',
            'name' => 'Admin Ticketing',
            'email' => 'ticket-dept-admin@example.test',
            'whatsapp_number' => '08123456789',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $service = Service::create([
            'department_name' => 'Dinas Kesehatan',
            'service_code' => 'TICKET-DEPT-001',
            'name' => 'Layanan Kesehatan',
        ]);

        Ticket::create([
            'ticket_no' => 'SLA-TICKET-DEPT-001',
            'user_id' => $admin->id,
            'service_id' => $service->id,
            'title' => 'Kendala layanan',
            'description' => 'Deskripsi kendala.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.ticketing'))
            ->assertOk()
            ->assertSee('Layanan Kesehatan')
            ->assertSee('Dinas Kesehatan');
    }
}
