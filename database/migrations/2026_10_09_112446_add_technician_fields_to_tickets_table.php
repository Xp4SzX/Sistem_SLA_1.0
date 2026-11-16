<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'technician_notes')) {
                $table->text('technician_notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('tickets', 'technician_file')) {
                $table->string('technician_file')->nullable()->after('technician_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['technician_notes', 'technician_file']);
        });
    }
};