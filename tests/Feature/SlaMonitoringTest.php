<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SlaMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_services_and_their_departments_across_all_departments(): void
    {
        $admin = $this->createAdmin();
        $this->createDepartment('Dinas Kesehatan');
        $this->createDepartment('Dinas Pendidikan');

        Service::create([
            'department_name' => 'Dinas Kesehatan',
            'service_code' => 'KES001',
            'name' => 'Layanan Kesehatan',
            'status' => 'active',
        ]);
        Service::create([
            'department_name' => 'Dinas Pendidikan',
            'service_code' => 'PDD001',
            'name' => 'Layanan Pendidikan',
            'status' => 'maintenance',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.sla-monitoring'));

        $response->assertOk()
            ->assertSee('Layanan Kesehatan')
            ->assertSee('Dinas Kesehatan')
            ->assertSee('Layanan Pendidikan')
            ->assertSee('Dinas Pendidikan')
            ->assertViewHas('kpi', fn (array $kpi) => $kpi['services'] === 2);
    }

    public function test_department_filter_filters_services_and_sla_ticket_totals_by_department(): void
    {
        $admin = $this->createAdmin();
        $departmentId = $this->createDepartment('Dinas Kesehatan');
        $this->createDepartment('Dinas Pendidikan');

        $healthService = Service::create([
            'department_name' => 'Dinas Kesehatan',
            'service_code' => 'KES002',
            'name' => 'Layanan Kesehatan',
            'status' => 'active',
        ]);
        Service::create([
            'department_name' => 'Dinas Pendidikan',
            'service_code' => 'PDD002',
            'name' => 'Layanan Pendidikan',
            'status' => 'active',
        ]);

        Ticket::create([
            'ticket_no' => 'SLA-HEALTH-001',
            'user_id' => $admin->id,
            'service_id' => $healthService->id,
            'title' => 'Tiket Kesehatan',
            'description' => 'Tiket untuk layanan kesehatan.',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.sla-monitoring', [
            'department_id' => $departmentId,
        ]));

        $response->assertOk()
            ->assertSee('Layanan Kesehatan')
            ->assertDontSee('Layanan Pendidikan')
            ->assertViewHas('kpi', fn (array $kpi) => $kpi['services'] === 1 && $kpi['tickets'] === 1)
            ->assertViewHas('chartMonthlyData', fn (array $data) => array_sum($data) === 1);
    }

    private function createDepartment(string $name): int
    {
        return DB::table('dinas_admins')->insertGetId([
            'nama_dinas' => $name,
            'name' => "Admin {$name}",
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.test',
            'whatsapp' => '08123456789',
            'penanggung_jawab' => "Penanggung Jawab {$name}",
            'password' => 'password',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createAdmin(): User
    {
        return User::create([
            'identity_number' => 'SLA-MONITOR-ADMIN',
            'name' => 'Admin Monitoring',
            'email' => 'sla-monitor-admin@example.test',
            'whatsapp_number' => '08123456789',
            'password' => 'password',
            'role' => 'admin',
        ]);
    }
}
