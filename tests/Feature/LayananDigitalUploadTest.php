<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LayananDigitalUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_logo_and_sop_file(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Storage::fake('public');

        $response = $this->actingAs($admin)
            ->post(route('admin.layanandigital.store'), [
                'name' => 'E-Planning KBB',
                'service_code' => 'EPLN01',
                'department_name' => 'Bappelitbangda',
                'type' => 'pelayanan_publik',
                'description' => 'Sistem perencanaan pembangunan daerah',
                'response_time' => '30 menit',
                'resolution_time' => '8 jam',
                'valid_from' => '2026-01-01',
                'valid_until' => '2027-01-01',
                'image' => UploadedFile::fake()->image('logo.png', 200, 200),
                'sop_file' => UploadedFile::fake()->create('sop.pdf', 2000),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('services', [
            'name' => 'E-Planning KBB',
            'service_code' => 'EPLN01',
        ]);

        $service = Service::first();
        $this->assertNotNull($service->image);
        $this->assertNotNull($service->sop_file);
        Storage::disk('public')->assertExists($service->image);
        Storage::disk('public')->assertExists($service->sop_file);
    }

    public function test_updating_a_service_redirects_to_the_index_instead_of_the_put_endpoint(): void
    {
        $admin = new User();
        $admin->id = 1;
        $admin->role = 'admin';
        $service = Service::create([
            'name' => 'Layanan Lama',
            'service_code' => 'LAMA01',
            'type' => 'pelayanan_publik',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->withHeader('Referer', route('admin.layanandigital.update', $service->id))
            ->put(route('admin.layanandigital.update', $service->id), [
                'name' => 'Layanan Baru',
                'service_code' => 'BARU01',
                'type' => 'pelayanan_publik',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.layanandigital'));
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Layanan Baru',
        ]);
    }
}
