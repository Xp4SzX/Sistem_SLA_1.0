<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomePageDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_active_services_and_ticket_statistics(): void
    {
        $departmentId = DB::table('departments')->insertGetId([
            'code' => 'HOME-TEST',
            'name' => 'Home Page Test Department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('services')->insert([
            'department_id' => $departmentId,
            'service_code' => 'HOME-SVC',
            'name' => 'Home Page Test Service',
            'status' => 'active',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Home Page Test Service')
            ->assertSee('Total 1 Layanan Aktif')
            ->assertSee('Total Pengaduan');
    }
}
