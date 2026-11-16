<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OperatorTicketFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_only_sees_their_service_tickets_and_updates_return_to_ticketing(): void
    {
        $departmentId = DB::table('departments')->insertGetId([
            'code' => 'TEST',
            'name' => 'Test Department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $operatorId = DB::table('users')->insertGetId([
            'department_id' => $departmentId,
            'identity_number' => 'OP-HOSTING-TEST',
            'name' => 'Hosting Operator',
            'email' => 'hosting.operator@example.test',
            'whatsapp_number' => '08123456789',
            'password' => Hash::make('password'),
            'role' => 'hosting',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $operator = User::findOrFail($operatorId);

        $hostingService = Service::create([
            'department_id' => $departmentId,
            'operator_role' => 'hosting',
            'service_code' => 'HOST-TEST',
            'name' => 'Hosting Service',
        ]);

        $otherService = Service::create([
            'department_id' => $departmentId,
            'operator_role' => 'pembangunan',
            'service_code' => 'DEV-TEST',
            'name' => 'Development Service',
        ]);

        $hostingTicket = Ticket::create([
            'ticket_no' => 'SLA-HOST-TEST',
            'user_id' => $operator->id,
            'service_id' => $hostingService->id,
            'title' => 'Hosting ticket',
            'description' => 'Ticket for hosting operator',
            'category' => 'network',
        ]);

        Ticket::create([
            'ticket_no' => 'SLA-OTHER-TEST',
            'user_id' => $operator->id,
            'service_id' => $otherService->id,
            'title' => 'Other department ticket',
            'description' => 'Ticket outside hosting operator',
            'category' => 'hosting',
        ]);

        $response = $this->actingAs($operator)->get(route('operator.tickets'));

        $response->assertOk()
            ->assertSee('Hosting ticket')
            ->assertDontSee('Other department ticket');

        $response = $this->get(route('operator.tickets.update-status', $hostingTicket->id));
        $response->assertRedirect(route('operator.tickets'));

        $response = $this->from(route('operator.tickets.update-status', $hostingTicket->id))
            ->post(route('operator.tickets.update-status', $hostingTicket->id), [
                '_method' => 'PUT',
                'status' => 'in_progress',
                'technician' => 'Hosting Technician',
            ]);

        $response->assertRedirect(route('operator.tickets'));
        $this->assertDatabaseHas('tickets', [
            'id' => $hostingTicket->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_operator_uploads_progress_evidence_and_reporter_can_view_it(): void
    {
        Storage::fake('public');

        $departmentId = DB::table('departments')->insertGetId([
            'code' => 'EVIDENCE',
            'name' => 'Evidence Test Department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $operatorId = DB::table('users')->insertGetId([
            'department_id' => $departmentId,
            'identity_number' => 'OP-EVIDENCE-TEST',
            'name' => 'Evidence Operator',
            'email' => 'evidence.operator@example.test',
            'whatsapp_number' => '08123456781',
            'password' => Hash::make('password'),
            'role' => 'hosting',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $reporterId = DB::table('users')->insertGetId([
            'identity_number' => 'REPORTER-EVIDENCE-TEST',
            'name' => 'Evidence Reporter',
            'email' => 'evidence.reporter@example.test',
            'whatsapp_number' => '08123456782',
            'password' => Hash::make('password'),
            'role' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = Service::create([
            'department_id' => $departmentId,
            'operator_role' => 'hosting',
            'service_code' => 'EVIDENCE-TEST',
            'name' => 'Evidence Test Service',
        ]);
        $ticket = Ticket::create([
            'ticket_no' => 'SLA-EVIDENCE-TEST',
            'user_id' => $reporterId,
            'service_id' => $service->id,
            'title' => 'Evidence test ticket',
            'description' => 'Ticket used to verify evidence upload',
            'category' => 'hosting',
        ]);

        $this->actingAs(User::findOrFail($operatorId))
            ->put(route('operator.tickets.update-status', $ticket->id), [
                'status' => 'resolved',
                'technician' => 'Evidence Technician',
                'catatan' => 'Perbaikan sudah selesai.',
                'attachment' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('operator.tickets'));

        $ticket->refresh();
        $this->assertSame('Perbaikan sudah selesai.', $ticket->technician_notes);
        $this->assertNotEmpty($ticket->technician_file);
        Storage::disk('public')->assertExists($ticket->technician_file);
        $this->assertDatabaseHas('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'Update Progress & Bukti Penanganan',
            'description' => 'Perbaikan sudah selesai.',
        ]);

        $this->actingAs(User::findOrFail($reporterId))
            ->get(route('user.ticketing'))
            ->assertOk()
            ->assertSee('Perbaikan sudah selesai.')
            ->assertSee(str_replace('/', '\\/', $ticket->technician_file));
    }
}
