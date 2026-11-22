<?php

namespace App\Http\Controllers\PerangkatDaerah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Service;
use App\Models\DinasAdmin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        
        // Ambil nama dinas secara pasti dari tabel dinas_admins berdasarkan email user yang login
        $dinasAdmin = DinasAdmin::where('email', $admin->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : ($admin->name ?? 'Pemerintah Daerah');

        // Filter tiket berdasarkan relasi service dengan department_name yang sesuai
        $ticketsQuery = Ticket::query()->with(['service'])->whereHas('service', function($q) use ($departmentName) {
            $q->whereRaw('LOWER(department_name) = ?', [strtolower(trim($departmentName))]);
        });
        
        $tickets = $ticketsQuery->get();

        $totalTicket = $tickets->count();
        $dalamProses = $tickets->where('status', 'in_progress')->count();
        $selesai = $tickets->whereIn('status', ['resolved', 'closed', 'completed'])->count();
        
        // Penghitungan Dinamis Status SLA & Kepatuhan
        $mendekatiSla = 0; 
        $melewatiSla = 0;  
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
                    // Mendekati SLA (jika sisa waktu kurang dari 2 jam menuju deadline)
                    $mendekatiSla++;
                }
            }
        }

        // Kalkulasi persentase kepatuhan SLA
        $kepatuhanSlaNum = $totalTicket > 0 ? max(0, round((($totalTicket - $melewatiSla) / $totalTicket) * 100)) : 100;
        $kepatuhanSla = $kepatuhanSlaNum . '%';

        $rekapData = array_fill(0, 12, 0);
        $currentYear = Carbon::now()->year;

        foreach ($tickets as $ticket) {
            if ($ticket->created_at) {
                $createdAt = Carbon::parse($ticket->created_at);
                if ($createdAt->year === $currentYear) {
                    $rekapData[$createdAt->month - 1]++;
                }
            }
        }

        $pUrgent = $tickets->filter(fn ($ticket) => strtolower((string) $ticket->priority) === 'urgent')->count();
        $pHigh = $tickets->filter(fn ($ticket) => strtolower((string) $ticket->priority) === 'high')->count();
        $pMedium = $tickets->filter(fn ($ticket) => strtolower((string) $ticket->priority) === 'medium')->count();
        $pLow = $tickets->filter(fn ($ticket) => strtolower((string) $ticket->priority) === 'low')->count();

        $prioritasValues = [$pUrgent, $pHigh, $pMedium, $pLow];
        $totalPriority = max($totalTicket, 1);
        $prioritas = [
            'Urgent' => round(($pUrgent / $totalPriority) * 100) . '%',
            'High' => round(($pHigh / $totalPriority) * 100) . '%',
            'Medium' => round(($pMedium / $totalPriority) * 100) . '%',
            'Low' => round(($pLow / $totalPriority) * 100) . '%',
        ];
        
        // Hitung total layanan khusus untuk dinas ini
        $totalLayananDinas = Service::whereRaw('LOWER(department_name) = ?', [strtolower(trim($departmentName))])->count();

        $summarySla = [
            'avg_response'      => '0 mnt',
            'avg_resolution'    => '0 jam',
            'avg_uptime'        => '100%',
            'total_pelanggaran' => $melewatiSla,
            'kepatuhan_sla'     => $kepatuhanSla,
            'total_layanan'     => $totalLayananDinas,
        ];

        return view('perangkat_daerah.dashboard', compact(
            'totalTicket', 
            'dalamProses', 
            'selesai', 
            'mendekatiSla', 
            'melewatiSla', 
            'kepatuhanSla', 
            'rekapData', 
            'prioritasValues', 
            'prioritas', 
            'summarySla'
        ));
    }
}