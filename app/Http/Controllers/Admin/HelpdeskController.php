<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HelpdeskController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Kueri Dasar (Memuat relasi user, service, logs, dan attachments)
        $query = Ticket::with(['user', 'service', 'logs', 'attachments']);

        // 2. Filter Pencarian Teks (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Filter Berdasarkan Dropdown Status
        if ($request->filled('status')) {
            $query->where('status', strtolower($request->status));
        }

        // 4. Eksekusi Kueri
        $tickets = $query->latest()->get();

        $maxHours = 8; // Batas SLA = 8 Jam
        $now = Carbon::now();

        // Inisialisasi Counter Statistik
        $dalamProses  = 0;
        $mendekatiSla = 0;
        $melewatiSla  = 0;
        $selesai      = 0;

        // Iterasi & Hitung SLA Masing-Masing Tiket Secara Dinamis
        $tickets->transform(function ($ticket) use ($maxHours, $now, &$dalamProses, &$mendekatiSla, &$melewatiSla, &$selesai) {
            $createdAt = Carbon::parse($ticket->created_at);
            $deadline  = $createdAt->copy()->addHours($maxHours);

            // Cek apakah status tiket sudah selesai
            $isDone = in_array(strtolower($ticket->status), ['resolved', 'completed', 'closed']);

            if ($isDone) {
                $selesai++;
                $ticket->sisa_waktu  = 'Selesai';
                $ticket->sla_class   = 'text-emerald-600 font-semibold';
                $ticket->is_over_sla = false;
                $ticket->is_near_sla = false;
                return $ticket;
            }

            // Jika belum selesai, hitung sebagai tiket 'Dalam Proses'
            $dalamProses++;

            if ($now->greaterThan($deadline)) {
                // TIKET MELEWATI SLA
                $melewatiSla++;
                
                $diffMinutes = $deadline->diffInMinutes($now);
                if ($diffMinutes < 60) {
                    $ticket->sisa_waktu = "Lewat {$diffMinutes}m";
                } else {
                    $diffHours = intdiv($diffMinutes, 60);
                    $remMinutes = $diffMinutes % 60;
                    $ticket->sisa_waktu = "Lewat {$diffHours}j {$remMinutes}m";
                }

                $ticket->sla_class   = 'text-rose-600 font-bold';
                $ticket->is_over_sla = true;
                $ticket->is_near_sla = false;
            } else {
                // TIKET MASIH DALAM BATAS WAKTU SLA
                $remainingMinutes = $now->diffInMinutes($deadline);
                $diffHours   = intdiv($remainingMinutes, 60);
                $diffMinutes = $remainingMinutes % 60;

                $ticket->sisa_waktu = "{$diffHours}j {$diffMinutes}m";

                // Kategori MENDEKATI SLA jika sisa waktu <= 2 jam
                if ($diffHours <= 2) {
                    $mendekatiSla++;
                    $ticket->sla_class   = 'text-amber-600 font-bold';
                    $ticket->is_near_sla = true;
                    $ticket->is_over_sla = false;
                } else {
                    $ticket->sla_class   = 'text-slate-600 font-medium';
                    $ticket->is_near_sla = false;
                    $ticket->is_over_sla = false;
                }
            }

            return $ticket;
        });

        // Total keseluruhan tiket yang ada pada kueri saat ini
        $totalTicket = $tickets->count();

        return view('admin.ticketing', compact(
            'tickets',
            'totalTicket',
            'dalamProses',
            'mendekatiSla',
            'melewatiSla',
            'selesai'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'status'     => 'required|string',
            'technician' => 'nullable|string|max:255',
            'catatan'    => 'nullable|string'
        ]);

        $oldStatus = $ticket->status;
        $newStatus = strtolower($request->status);

        // Update status dan nama teknisi ke database
        $ticket->update([
            'status'     => $newStatus,
            'technician' => $request->technician,
        ]);

        if ($request->filled('catatan') || $request->filled('technician')) {
            TicketLog::create([
                'ticket_id'  => $ticket->id,
                'actor_id'   => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'catatan'    => $request->catatan ?? 'Memperbarui status dan penugasan teknisi.',
            ]);
        }

        return redirect()->back()->with('success', 'Tiket berhasil diperbarui!');
    }
}