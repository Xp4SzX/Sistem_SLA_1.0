<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed / Cek Admin User (Tanpa department_id)
        $admin = DB::table('users')->where('email', 'admin@bandungbaratkab.go.id')->first();
        if (!$admin) {
            DB::table('users')->insert([
                'identity_number' => '199501012026011001',
                'name' => 'Admin KBB',
                'email' => 'admin@bandungbaratkab.go.id',
                'whatsapp_number' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Seed / Cek Regular User (Arrafi Hilmi)
        $user = DB::table('users')->where('email', 'arrafi@gmail.com')->first();
        if (!$user) {
            DB::table('users')->insert([
                'identity_number' => '3217010101990001',
                'name' => 'Arrafi Hilmi',
                'email' => 'arrafi@gmail.com',
                'whatsapp_number' => '089876543210',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Seed Services (Menggunakan department_name sebagai teks langsung)
        $servicesData = [
            [
                'department_name' => 'Dinas Komunikasi, Informatika dan Statistik',
                'service_code' => 'PEMB-01',
                'name' => 'Sistem Informasi Pembangunan Daerah (SIPD)',
                'type' => 'pelayanan_publik',
                'description' => 'Aplikasi perencanaan dan penganggaran pembangunan daerah.',
                'response_time' => '30 menit',
                'resolution_time' => '8 jam',
                'jangka_waktu' => '2 Hari Kerja',
                'biaya' => 'Gratis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_name' => 'Dinas Komunikasi, Informatika dan Statistik',
                'service_code' => 'PEMB-ERR',
                'name' => 'Gangguan Akses & Error Modul SIPD / Monev',
                'type' => 'kendala',
                'description' => 'Pelaporan kendala teknis, error sistem, atau bug pada aplikasi Pembangunan & Monev.',
                'response_time' => '30 menit',
                'resolution_time' => '8 jam',
                'jangka_waktu' => '4 Jam',
                'biaya' => 'Gratis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_name' => 'Dinas Komunikasi, Informatika dan Statistik',
                'service_code' => 'SUB-01',
                'name' => 'Pendaftaran Nama Domain (*.bandungbaratkab.go.id)',
                'type' => 'pelayanan_publik',
                'description' => 'Layanan pembuatan domain resmi berakhiran go.id.',
                'response_time' => '30 menit',
                'resolution_time' => '8 jam',
                'jangka_waktu' => '3 Hari Kerja',
                'biaya' => 'Gratis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_name' => 'Dinas Komunikasi, Informatika dan Statistik',
                'service_code' => 'SUB-ERR',
                'name' => 'Gangguan DNS / Subdomain Tidak Bisa Diakses',
                'type' => 'kendala',
                'description' => 'Pelaporan domain/subdomain tidak dapat diakses atau error SSL/TLS.',
                'response_time' => '30 menit',
                'resolution_time' => '8 jam',
                'jangka_waktu' => '4 Jam',
                'biaya' => 'Gratis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($servicesData as $service) {
            $exists = DB::table('services')->where('service_code', $service['service_code'])->exists();
            if (!$exists) {
                DB::table('services')->insert($service);
            }
        }
    }
}