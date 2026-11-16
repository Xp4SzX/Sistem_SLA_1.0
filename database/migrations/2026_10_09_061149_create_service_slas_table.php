<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_slas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->decimal('target_uptime', 5, 2)->default(99.90);
            $table->integer('target_response_time')->default(30); // dalam menit
            $table->integer('target_resolution_time')->default(8); // dalam jam
            $table->date('effective_date')->nullable();
            $table->date('review_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_slas');
    }
};