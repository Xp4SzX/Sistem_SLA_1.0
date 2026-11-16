<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus foreign key dan kolom department_id dari tabel users terlebih dahulu
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'department_id')) {
                    // Hapus foreign constraint-nya dulu
                    $table->dropForeign(['department_id']);
                    // Hapus kolomnya
                    $table->dropColumn('department_id');
                }
            });
        }

        // 2. Sekarang aman untuk menghapus tabel departments
        Schema::dropIfExists('departments');

        // 3. Buat ulang tabel services dengan department_name (teks langsung)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('department_name')->default('Pemerintah Daerah'); 
            $table->string('service_code', 20)->unique();
            $table->string('name', 100);
            $table->string('type')->default('pelayanan_publik'); // pelayanan_publik / administrasi_pemerintahan
            $table->decimal('current_uptime', 5, 2)->default(100.00);
            $table->string('status')->default('active'); // active, maintenance, disabled
            $table->boolean('is_active')->default(true);
            $table->string('category', 100)->nullable();
            
            // Kolom Informasi & File Pendukung
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('sop_file')->nullable();
            $table->string('response_time')->default('30 menit');
            $table->string('resolution_time')->default('8 jam');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            // === 14 KOMPONEN STANDAR PELAYANAN (Sesuai Form Blade) ===
            $table->text('dasar_hukum')->nullable();
            $table->text('persyaratan')->nullable();
            $table->text('sistem_prosedur')->nullable();
            $table->string('biaya')->default('Gratis');
            $table->string('produk_pelayanan')->nullable();
            $table->text('sarana_prasarana')->nullable();
            $table->text('kompetensi_pelaksana')->nullable();
            $table->string('pengawasan_internal')->nullable();
            $table->string('penanganan_pengaduan')->nullable();
            $table->string('jumlah_pelaksana')->nullable();
            $table->text('jaminan_pelayanan')->nullable();
            $table->string('jaminan_keamanan')->nullable();
            $table->string('evaluasi_kinerja')->nullable();
            // ========================================================

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};