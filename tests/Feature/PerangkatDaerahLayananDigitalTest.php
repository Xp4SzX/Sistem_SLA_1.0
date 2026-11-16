<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PerangkatDaerahLayananDigitalTest extends TestCase
{
    public function test_layanan_digital_page_loads_when_department_has_no_services(): void
    {
        Schema::create('dinas_admins', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('nama_dinas');
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('department_name');
            $table->timestamps();
        });

        $user = new User([
            'name' => 'Admin Dinas',
            'email' => 'admin@example.test',
            'role' => 'admin_dinas',
        ]);
        $user->id = 1;

        $this->withoutVite()
            ->actingAs($user)
            ->get(route('perangkat-daerah.layanandigital'))
            ->assertOk()
            ->assertSee('Pilih Layanan')
            ->assertSee('Belum tersedia');
    }
}
