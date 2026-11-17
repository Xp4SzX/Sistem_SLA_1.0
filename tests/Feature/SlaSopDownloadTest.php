<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SlaSopDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_pelapor_can_download_the_sop_file_for_a_service(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('sop_files/portal-pelayanan.pdf', 'SOP content');

        $user = User::create([
            'identity_number' => 'SLA-SOP-DOWNLOAD-USER',
            'name' => 'Pelapor SOP',
            'email' => 'pelapor-sop@example.test',
            'whatsapp_number' => '08123456789',
            'password' => 'password',
            'role' => 'user',
        ]);

        $service = Service::create([
            'department_name' => 'Dinas Kesehatan',
            'service_code' => 'SOP001',
            'name' => 'Portal Pelayanan',
            'sop_file' => 'sop_files/portal-pelayanan.pdf',
        ]);

        $this->actingAs($user)
            ->get(route('sla-sop.download', $service))
            ->assertOk()
            ->assertDownload('portal-pelayanan-SOP.pdf');
    }
}
