<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->string('identity_number', 30)->unique();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('whatsapp_number', 20);
            $table->string('password');
            $table->string('role')->default('user'); // admin, pembangunan, hosting, subdomain, informasi_publik, user
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};