<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TicketCreationPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_reporter_can_load_ticket_form_with_departments(): void
    {
        DB::table('departments')->insert([
            'code' => 'TICKET-TEST',
            'name' => 'Ticket Test Department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $reporterId = DB::table('users')->insertGetId([
            'identity_number' => 'REPORTER-TICKET-TEST',
            'name' => 'Ticket Test Reporter',
            'email' => 'ticket.reporter@example.test',
            'whatsapp_number' => '08123456789',
            'password' => Hash::make('password'),
            'role' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs(User::findOrFail($reporterId))
            ->get(route('create-ticket'))
            ->assertOk()
            ->assertSee('Ticket Test Department');
    }
}
