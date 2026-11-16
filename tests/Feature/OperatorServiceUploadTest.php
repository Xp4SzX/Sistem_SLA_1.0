<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OperatorServiceUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_upload_and_replace_service_files_and_users_can_access_them(): void
    {
        Storage::fake('public');

        $departmentId = DB::table('departments')->insertGetId([
            'code' => 'OP-UPLOAD',
            'name' => 'Upload Test Department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $operatorId = DB::table('users')->insertGetId([
            'department_id' => $departmentId,
            'identity_number' => 'OP-UPLOAD-TEST',
            'name' => 'Upload Test Operator',
            'email' => 'upload.operator@example.test',
            'whatsapp_number' => '08123456780',
            'password' => Hash::make('password'),
            'role' => 'pembangunan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $operator = User::findOrFail($operatorId);

        $this->actingAs($operator)
            ->post(route('operator.layanandigital.store'), [
                'name' => 'Upload Test Service',
                'service_code' => 'UP-TEST',
                'type' => 'pelayanan_publik',
                'image' => UploadedFile::fake()->image('logo.png'),
                'sop_file' => UploadedFile::fake()->create('guide.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('operator.layanandigital'));

        $service = Service::where('service_code', 'UP-TEST')->firstOrFail();
        $this->assertNotNull($service->image);
        $this->assertNotNull($service->sop_file);
        Storage::disk('public')->assertExists($service->image);
        Storage::disk('public')->assertExists($service->sop_file);

        $oldImage = $service->image;
        $oldSop = $service->sop_file;

        $this->post(route('operator.layanandigital.update', $service->id), [
            '_method' => 'PUT',
            'name' => $service->name,
            'service_code' => $service->service_code,
            'type' => $service->type,
            'image' => UploadedFile::fake()->image('replacement.png'),
            'sop_file' => UploadedFile::fake()->create('replacement.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('operator.layanandigital'));

        $service->refresh();
        $this->assertNotSame($oldImage, $service->image);
        $this->assertNotSame($oldSop, $service->sop_file);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertMissing($oldSop);
        Storage::disk('public')->assertExists($service->image);
        Storage::disk('public')->assertExists($service->sop_file);

        $this->get(route('sla-sop'))
            ->assertOk()
            ->assertSee('storage/' . $service->image, false)
            ->assertSee('storage/' . $service->sop_file, false);
    }
}
