<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DinasAdmin; // Menggunakan DinasAdmin sebagai pengganti Department
use App\Models\Service;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class SlaMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Query Dasar Tiket dengan Filter
        // Karena relasi department pada user sudah dihapus, filter disesuaikan jika diperlukan
        $ticketQuery = Ticket::with(['user', 'service']);

        if ($request->filled('department_id')) {
            // Disesuaikan dengan struktur database baru jika ingin memfilter berdasarkan dinas
            $ticketQuery->whereHas('user', function ($q) use ($request) {
                $q->where('id', $request->department_id);
            });
        }

        if ($request->filled('service_status')) {
            $ticketQuery->whereHas('service', function ($q) use ($request) {
                $q->where('status', $request->service_status);
            });
        }

        $tickets = $ticketQuery->latest()->get();

        // 2. Logika Hitung SLA Tiket (Batas SLA = 8 Jam)
        $onTrackCount = 0;
        $warningCount = 0;
        $overdueCount = 0;
        $maxHours = 8; 
        $now = Carbon::now();

        foreach ($tickets as $ticket) {
            $createdAt = Carbon::parse($ticket->created_at);
            $deadline  = $createdAt->copy()->addHours($maxHours);
            $isDone = in_array(strtolower($ticket->status), ['resolved', 'completed', 'closed']);

            if ($isDone) {
                $onTrackCount++;
                continue;
            }

            if ($now->greaterThan($deadline)) {
                $overdueCount++;
            } else {
                $remainingMinutes = $now->diffInMinutes($deadline);
                $diffHours = intdiv($remainingMinutes, 60);

                if ($diffHours <= 2) {
                    $warningCount++;
                } else {
                    $onTrackCount++;
                }
            }
        }

        $totalTickets = $tickets->count();
        
        // Persentase Kepatuhan SLA
        $complianceRate = $totalTickets > 0 
            ? round((($totalTickets - $overdueCount) / $totalTickets) * 100, 1) 
            : 100;

        $kpi = [
            'services'   => Service::where('status', '!=', 'disabled')->count(),
            'tickets'    => $totalTickets,
            'on_track'   => $onTrackCount,
            'warning'    => $warningCount,
            'overdue'    => $overdueCount,
            'compliance' => $complianceRate,
        ];

        // 3. Data Grafik Batang Real-time (Jumlah Tiket Per Bulan di Tahun Berjalan)
        $monthlyTickets = Ticket::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [date('Y')])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $chartMonthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartMonthlyData[] = $monthlyTickets[$m] ?? 0;
        }

        // 4. Data Dropdown Filter & Ringkasan Layanan (Menggunakan DinasAdmin dan menghapus relasi department lama)
        $departments = DinasAdmin::select('id', 'nama_dinas as name')->orderBy('nama_dinas')->get();
        $services = Service::with(['tickets'])->get();

        return view('admin.sla-monitoring', compact(
            'tickets',
            'services',
            'departments',
            'kpi',
            'chartMonthlyData'
        ));
    }
}