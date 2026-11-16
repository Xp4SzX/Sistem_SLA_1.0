<?php

namespace App\Http\Controllers\PerangkatDaerah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Service;
use App\Models\DinasAdmin;
use Carbon\Carbon;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil nama dinas dari tabel dinas_admins berdasarkan email admin yang login
        $dinasAdmin = DinasAdmin::where('email', $user->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : ($user->name ?? 'Pemerintah Daerah');

        // Query dasar: Ambil tiket yang relasi layanan (service)-nya memiliki department_name yang sesuai
        $ticketsQuery = Ticket::with(['service', 'user'])
            ->whereHas('service', function($q) use ($departmentName) {
                $q->whereRaw('LOWER(department_name) = ?', [strtolower(trim($departmentName))]);
            });

        // Fitur Filter Pencarian (Search & Status) jika digunakan
        if ($request->filled('search')) {
            $search = $request->search;
            $ticketsQuery->where(function($q) use ($search) {
                $q->where('ticket_no', 'ILIKE', "%{$search}%")
                  ->orWhere('title', 'ILIKE', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'ILIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $ticketsQuery->where('status', $request->status);
        }

        $tickets = $ticketsQuery->latest()->get();
        
        // Statistik kartu ringkasan
        $totalTicket  = $tickets->count();
        $dalamProses  = $tickets->where('status', 'in_progress')->count();
        $selesai      = $tickets->whereIn('status', ['resolved', 'closed', 'completed'])->count();
        
        // Kalkulasi dinamis Mendekati SLA dan Melewati SLA
        $mendekatiSla = 0;
        $melewatiSla  = 0;
        $now = Carbon::now();

        foreach ($tickets as $ticket) {
            $isDone = in_array(strtolower($ticket->status), ['resolved', 'completed', 'closed']);
            if (!$isDone) {
                $rawJangkaWaktu = $ticket->service->jangka_waktu ?? '8 Jam';
                preg_match('/\d+/', $rawJangkaWaktu, $matches);
                $jumlahAngka = isset($matches[0]) ? (int)$matches[0] : 8;
                
                $isHari = (stripos($rawJangkaWaktu, 'hari') !== false);
                $limitJam = $isHari ? ($jumlahAngka * 24) : $jumlahAngka;

                $createdAt = Carbon::parse($ticket->created_at);
                $deadline = $createdAt->copy()->addHours($limitJam);

                if ($now->greaterThan($deadline)) {
                    $melewatiSla++;
                } elseif ($now->copy()->addHours(2)->greaterThan($deadline)) {
                    $mendekatiSla++;
                }
            }
        }

        return view('perangkat_daerah.ticketing', compact(
            'tickets', 
            'totalTicket', 
            'dalamProses', 
            'selesai', 
            'mendekatiSla', 
            'melewatiSla'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        
        $ticket->update([
            'status'     => $request->status,
            'technician' => $request->technician ?? $ticket->technician,
        ]);

        return redirect()->back()->with('success', 'Status tiket berhasil diperbarui.');
    }
}