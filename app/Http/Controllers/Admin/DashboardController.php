<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['service', 'user', 'logs'])->latest()->get();
        $maxHours = 8; 
        $now = Carbon::now();

        $dalamProses  = 0;
        $mendekatiSla = 0;
        $melewatiSla  = 0;
        $selesai      = 0;
        
        $totalResponseMinutes = 0;
        $responseCount = 0;
        $totalResolutionMinutes = 0;
        $resolutionCount = 0;

        foreach ($tickets as $ticket) {
            $isDone = in_array(strtoupper($ticket->status), ['RESOLVED', 'COMPLETED', 'CLOSED']);

            $firstLog = $ticket->logs->sortBy('created_at')->first();
            if ($firstLog) {
                $created = Carbon::parse($ticket->created_at);
                $responded = Carbon::parse($firstLog->created_at);
                $totalResponseMinutes += $created->diffInMinutes($responded);
                $responseCount++;
            }

            if ($isDone) {
                $selesai++;
                $created = Carbon::parse($ticket->created_at);
                $finished = Carbon::parse($ticket->updated_at);
                $totalResolutionMinutes += $created->diffInMinutes($finished);
                $resolutionCount++;
                continue;
            }

            $dalamProses++;
            $createdAt = Carbon::parse($ticket->created_at);
            $deadline  = $createdAt->copy()->addHours($maxHours);

            if ($now->greaterThan($deadline)) {
                $melewatiSla++;
            } else {
                $remainingMinutes = $now->diffInMinutes($deadline);
                $diffHours = intdiv($remainingMinutes, 60);
                if ($diffHours < 2) {
                    $mendekatiSla++;
                }
            }
        }

        $totalTicket = $tickets->count();

        // Rata-rata Waktu
        $avgResponseMins = $responseCount > 0 ? round($totalResponseMinutes / $responseCount) : 0;
        $avgResponseStr = $avgResponseMins >= 60 ? round($avgResponseMins / 60, 1) . ' jam' : $avgResponseMins . ' mnt';

        $avgResolutionMins = $resolutionCount > 0 ? round($totalResolutionMinutes / $resolutionCount) : 0;
        $avgResolutionStr = $avgResolutionMins >= 60 ? round($avgResolutionMins / 60, 1) . ' jam' : $avgResolutionMins . ' mnt';

        // Kepatuhan SLA
        $kepatuhanSlaNum = $totalTicket > 0 ? round((($totalTicket - $melewatiSla) / $totalTicket) * 100) : 100;
        $kepatuhanSla = $kepatuhanSlaNum . '%';

        // --- PERHITUNGAN PERSENTASE DINAMIS (Bulan Ini vs Bulan Lalu) ---
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $lastMonth = $now->copy()->subMonth()->month;
        $lastMonthYear = $now->copy()->subMonth()->year;

        // Helper untuk hitung persentase pertumbuhan aman dari division by zero
        $calcGrowth = function($thisMonthVal, $lastMonthVal) {
            if ($lastMonthVal > 0) {
                $growth = (($thisMonthVal - $lastMonthVal) / $lastMonthVal) * 100;
                return ($growth >= 0 ? '+' : '') . round($growth, 1) . '%';
            }
            return $thisMonthVal > 0 ? '+100%' : '0%';
        };

        // 1. Total Ticket Growth
        $tThis = Ticket::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $tLast = Ticket::whereMonth('created_at', $lastMonth)->whereYear('created_at', $lastMonthYear)->count();
        $ticketGrowth = $calcGrowth($tThis, $tLast);

        // 2. Dalam Proses Growth
        $dpThis = Ticket::whereNotIn('status', ['resolved', 'closed', 'completed'])->whereMonth('created_at', $currentMonth)->count();
        $dpLast = Ticket::whereNotIn('status', ['resolved', 'closed', 'completed'])->whereMonth('created_at', $lastMonth)->count();
        $prosesGrowth = $calcGrowth($dpThis, $dpLast);

        // 3. Selesai Growth
        $sThis = Ticket::whereIn('status', ['resolved', 'closed', 'completed'])->whereMonth('created_at', $currentMonth)->count();
        $sLast = Ticket::whereIn('status', ['resolved', 'closed', 'completed'])->whereMonth('created_at', $lastMonth)->count();
        $selesaiGrowth = $calcGrowth($sThis, $sLast);

        // 4. Melewati SLA Growth (Berdasarkan pelanggaran bulan ini vs bulan lalu)
        // (Bisa disesuaikan dengan logika filter tanggal atau dibiarkan dinamis)
        $pelanggaranGrowth = '+0%'; // atau hitung berdasarkan log pelanggaran

        // Donut Chart & Rekap Bulanan
        $urgentCount = $tickets->filter(fn($t) => strtolower($t->priority) === 'urgent')->count();
        $highCount   = $tickets->filter(fn($t) => strtolower($t->priority) === 'high')->count();
        $mediumCount = $tickets->filter(fn($t) => strtolower($t->priority) === 'medium')->count();
        $lowCount    = $tickets->filter(fn($t) => strtolower($t->priority) === 'low')->count();

        $prioritasValues = [$urgentCount, $highCount, $mediumCount, $lowCount];
        $prioritas = [
            'Urgent' => $totalTicket > 0 ? round(($urgentCount / $totalTicket) * 100) . '%' : '0%',
            'High'   => $totalTicket > 0 ? round(($highCount / $totalTicket) * 100) . '%' : '0%',
            'Medium' => $totalTicket > 0 ? round(($mediumCount / $totalTicket) * 100) . '%' : '0%',
            'Low'    => $totalTicket > 0 ? round(($lowCount / $totalTicket) * 100) . '%' : '0%',
        ];

        $rekapData = array_fill(0, 12, 0);
        foreach ($tickets as $ticket) {
            $ticketCreatedAt = Carbon::parse($ticket->created_at);
            if ($ticketCreatedAt->format('Y') == $currentYear) {
                $rekapData[(int)$ticketCreatedAt->format('m') - 1]++;
            }
        }

        $layanan = Service::all();
        $summarySla = [
            'avg_response'      => $avgResponseStr,
            'avg_resolution'    => $avgResolutionStr,
            'avg_uptime'        => '99.8%',
            'total_pelanggaran' => $melewatiSla,
            'kepatuhan_sla'     => $kepatuhanSla,
            'total_layanan'     => $layanan->count(),
        ];

        return view('admin.dashboard', compact(
            'totalTicket',
            'dalamProses',
            'selesai',
            'melewatiSla',
            'mendekatiSla',
            'kepatuhanSla',
            'ticketGrowth',
            'prosesGrowth',
            'selesaiGrowth',
            'prioritasValues',
            'prioritas',
            'rekapData',
            'layanan',
            'summarySla'
        ));
    }
}