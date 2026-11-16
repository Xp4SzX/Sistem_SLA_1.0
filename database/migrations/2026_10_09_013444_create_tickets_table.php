<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Relasi ke layanan (dari struktur asli SLA_2.sql)
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->onDelete('set null');
            
            // === TAMBAHAN PERUBAHAN BARU (Pemisahan SK vs Modul) ===
            $table->enum('request_type', ['pengajuan_layanan', 'kendala_teknis'])->default('kendala_teknis');
            $table->string('service_category', 50)->nullable(); // pembangunan, hosting, subdomain, informasi_publik (untuk SK)
            $table->string('problem_category', 50)->nullable(); // backend, ui_ux, network, database, dll. (untuk Kendala)
            // ========================================================
            
            $table->string('title', 150);
            $table->text('description');
            $table->string('image')->nullable();
            
            $table->string('category')->default('backend'); // Kompatibilitas dengan struktur asli
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('submitted'); // submitted, verified, assigned, in_progress, resolved, closed
            $table->string('sla_status')->default('on_track'); // on_track, warning, overdue
            
            $table->timestamp('first_responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->smallInteger('rating')->nullable();
            $table->text('rating_feedback')->nullable();
            $table->string('technician')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'sla_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};