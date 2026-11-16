<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dinas_admins', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dinas'); // Nama Dinas / Instansi
            $table->string('name');       // Nama Admin Dinas
            $table->string('email')->unique();
            $table->string('whatsapp');   // Nomer WA
            $table->string('penanggung_jawab'); // Penanggung Jawab
            $table->string('password');   // Password akun
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dinas_admins');
    }
};

