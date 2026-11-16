<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Halaman Dashboard Teknisi
     */
    public function index()
    {
        $technician = Auth::user();

        $query = Ticket::query();
        
        // Filter fleksibel berdasarkan department_id atau department_name
        if (!empty($technician->department_id)) {
            $query->whereHas('service', function($q) use ($technician) {
                $q->where('department_id', $technician->department_id);
            });
        } elseif (!empty($technician->department_name)) {
            $query->whereHas('service', function($q) use ($technician) {
                $q->whereRaw('LOWER(department_name) = ?', [strtolower(trim($technician->department_name))]);
            });
        }

        $tickets = $query->get();

        $totalTicket = $tickets->count();
        $dalamProses = $tickets->whereIn('status', ['in_progress', 'process', 'verified'])->count();
        $selesai     = $tickets->whereIn('status', ['resolved', 'closed', 'completed'])->count();
        
        $mendekatiSla = 0; 
        $melewatiSla  = 0;

        $kepatuhanSla = $totalTicket > 0 ? round(($selesai / $totalTicket) * 100) . '%' : '0%';

        $rekapData = array_fill(0, 12, 0);
        foreach ($tickets as $ticket) {
            if ($ticket->created_at) {
                $month = (int) $ticket->created_at->format('n') - 1;
                $rekapData[$month]++;
            }
        }

        $pUrgent = $tickets->where('priority', 'urgent')->count();
        $pHigh   = $tickets->where('priority', 'high')->count();
        $pMedium = $tickets->where('priority', 'medium')->count();
        $pLow    = $tickets->where('priority', 'low')->count();

        $prioritasValues = [$pUrgent, $pHigh, $pMedium, $pLow];
        $totalPrio = max($totalTicket, 1);

        $prioritas = [
            'Urgent' => round(($pUrgent / $totalPrio) * 100) . '%',
            'High'   => round(($pHigh / $totalPrio) * 100) . '%',
            'Medium' => round(($pMedium / $totalPrio) * 100) . '%',
            'Low'    => round(($pLow / $totalPrio) * 100) . '%',
        ];

        return view('teknisi.dashboard', compact(
            'totalTicket', 'dalamProses', 'selesai', 'mendekatiSla', 
            'melewatiSla', 'kepatuhanSla', 'rekapData', 'prioritasValues', 'prioritas'
        ));
    }

    /**
     * Halaman Manajemen Ticketing Teknisi (Sudah difilter sesuai dinas teknisi)
     */
    public function ticketing(Request $request)
    {
        $technician = Auth::user();

        $query = Ticket::with(['service', 'user', 'attachments']);
        
        // Filter fleksibel berdasarkan department_id atau department_name
        if (!empty($technician->department_id)) {
            $query->whereHas('service', function($q) use ($technician) {
                $q->where('department_id', $technician->department_id);
            });
        } elseif (!empty($technician->department_name)) {
            $query->whereHas('service', function($q) use ($technician) {
                $q->whereRaw('LOWER(department_name) = ?', [strtolower(trim($technician->department_name))]);
            });
        }

        // Fitur Pencarian (Search)
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

        // Fitur Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->get();

        $totalTicket  = $tickets->count();
        $dalamProses  = $tickets->whereIn('status', ['in_progress', 'process', 'verified'])->count();
        $selesai      = $tickets->whereIn('status', ['resolved', 'closed', 'completed'])->count();
        $mendekatiSla = 0;
        $melewatiSla  = 0;

        return view('teknisi.Ticketing', compact(
            'tickets', 'totalTicket', 'dalamProses', 'selesai', 'mendekatiSla', 'melewatiSla'
        ));
    }

    /**
     * Proses Update Status, Catatan, dan Upload Berkas Bukti dari Modal Teknisi
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'           => 'required|string',
            'technician'       => 'required|string',
            'technician_notes' => 'nullable|string',
            'technician_file'  => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
        ]);

        $ticket = Ticket::findOrFail($id);

        $dataUpdate = [
            'status'           => $request->status,
            'technician'       => $request->technician,
            'technician_notes' => $request->technician_notes,
        ];

        // Simpan file bukti penanganan jika diunggah oleh teknisi
        if ($request->hasFile('technician_file')) {
            $file = $request->file('technician_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('technician_files', $filename, 'public');
            
            $dataUpdate['technician_file'] = $path;
        }

        $ticket->update($dataUpdate);

        return redirect()->route('teknisi.ticketing')->with('success', 'Status dan penanganan tiket berhasil diperbarui.');
    }
}