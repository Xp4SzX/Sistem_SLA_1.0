<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\DinasAdmin;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil daftar nama dinas dari tabel dinas_admins untuk dropdown filter
        $departments = DinasAdmin::select('id', 'nama_dinas as name')->orderBy('nama_dinas')->get();

        $query = Service::with(['tickets']);

        // Filter berdasarkan department_name pada tabel services
        if ($request->filled('department') && $request->department != 'Semua Perangkat') {
            $deptName = $request->department;
            $query->where('department_name', $deptName);
        }

        $services = $query->get();

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;
        
        $maxHours = 8; 
        $now = Carbon::now();

        $services->transform(function ($service) use ($startDate, $endDate, $maxHours, $now) {
            $ticketsQuery = $service->tickets();

            if ($startDate && $endDate) {
                $ticketsQuery->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $ticketsQuery->where('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $ticketsQuery->where('created_at', '<=', $endDate);
            }

            $tickets = $ticketsQuery->get();
            $totalTickets = $tickets->count();

            $overdueCount = 0;
            if ($totalTickets > 0) {
                $overdueTickets = $tickets->filter(function ($ticket) use ($maxHours, $now, &$overdueCount) {
                    $isDone = in_array(strtoupper($ticket->status), ['RESOLVED', 'COMPLETED', 'CLOSED']);
                    if ($isDone) return false;
                    
                    $deadline = Carbon::parse($ticket->created_at)->addHours($maxHours);
                    $isOver = $now->greaterThan($deadline);
                    
                    if ($isOver) $overdueCount++;
                    return $isOver;
                });

                $kepatuhan = round((($totalTickets - $overdueTickets->count()) / $totalTickets) * 100, 1);
            } else {
                $kepatuhan = 100.0;
            }

            $service->total_tickets = $totalTickets;
            $service->overdue_tickets = $overdueCount;
            $service->kepatuhan_sla = $kepatuhan;
            return $service;
        });

        // Target minimal kepatuhan sesuai SK & Modul (99.9%)
        $targetMinimal = 99.9;

        if ($request->filled('status_sla') && $request->status_sla != 'Semua Status') {
            $status = strtolower($request->status_sla);
            $services = $services->filter(function($service) use ($status, $targetMinimal) {
                if ($status == 'terpenuhi' && $service->kepatuhan_sla >= $targetMinimal) return true;
                if ($status == 'mendekati' && $service->kepatuhan_sla >= 85 && $service->kepatuhan_sla < $targetMinimal) return true;
                if ($status == 'terlewati' && $service->kepatuhan_sla < 85) return true;
                return false;
            });
        }

        $totalLayanan = $services->count();
        $rataKepatuhan = $totalLayanan > 0 ? round($services->avg('kepatuhan_sla'), 1) : 100;
        
        $slaTerpenuhi = $services->where('kepatuhan_sla', '>=', $targetMinimal)->count();
        $slaMendekati = $services->whereBetween('kepatuhan_sla', [85, $targetMinimal - 0.1])->count();
        $slaTerlewati = $services->where('kepatuhan_sla', '<', 85)->count();
        $totalPelanggaran = $services->sum('overdue_tickets');

        // Hitung distribusi pelanggaran per kategori tiket secara dinamis
        $allTickets = Ticket::all();
        $totalAllViolations = 0;
        $categoryViolations = [];

        foreach ($allTickets as $ticket) {
            $isDone = in_array(strtoupper($ticket->status), ['RESOLVED', 'COMPLETED', 'CLOSED']);
            $deadline = Carbon::parse($ticket->created_at)->addHours($maxHours);
            
            if (!$isDone && $now->greaterThan($deadline)) {
                $cat = $ticket->category ?? 'umum';
                $categoryViolations[$cat] = ($categoryViolations[$cat] ?? 0) + 1;
                $totalAllViolations++;
            }
        }

        $categoryPercentages = [];
        foreach ($categoryViolations as $cat => $count) {
            $categoryPercentages[$cat] = $totalAllViolations > 0 ? round(($count / $totalAllViolations) * 100) : 0;
        }

        // Hitung tren kepatuhan SLA per bulan untuk tahun berjalan
        $currentYear = $now->year;
        $monthlyCompliance = [];
        
        for ($m = 1; $m <= 12; $m++) {
            $monthTickets = Ticket::whereMonth('created_at', $m)
                                   ->whereYear('created_at', $currentYear)
                                   ->get();
            
            $totalM = $monthTickets->count();
            if ($totalM > 0) {
                $overdueM = $monthTickets->filter(function ($t) use ($maxHours, $now) {
                    $isDone = in_array(strtoupper($t->status), ['RESOLVED', 'COMPLETED', 'CLOSED']);
                    if ($isDone) return false;
                    
                    $deadline = Carbon::parse($t->created_at)->addHours($maxHours);
                    return $now->greaterThan($deadline);
                })->count();

                $comp = round((($totalM - $overdueM) / $totalM) * 100);
                $monthlyCompliance[] = $comp;
            } else {
                $monthlyCompliance[] = 100;
            }
        }

        $pointsArray = [];
        $xStep = 700 / 11;
        
        foreach ($monthlyCompliance as $index => $val) {
            $x = round($index * $xStep, 1);
            $y = round(175 - (($val / 100) * 160), 1);
            $pointsArray[] = "{$x},{$y}";
        }
        $chartPoints = implode(' ', $pointsArray);

        // Koordinat dinamis untuk garis target minimal 99.9% pada grafik SVG
        $targetY = round(175 - (($targetMinimal / 100) * 160), 1);
        $targetPoints = "0,{$targetY} 700,{$targetY}";

        return view('admin.evaluasi', compact(
            'services', 
            'totalLayanan', 
            'rataKepatuhan', 
            'targetMinimal',
            'slaTerpenuhi', 
            'slaMendekati', 
            'slaTerlewati', 
            'totalPelanggaran',
            'departments',
            'categoryPercentages',
            'totalAllViolations',
            'chartPoints',
            'targetPoints'
        ));
    }

    public function exportPdf(Request $request)
    {
        $maxHours = 8; 
        $targetMinimal = 99.9;
        $services = Service::with(['tickets'])->get();
        $now = Carbon::now();

        $services->transform(function ($service) use ($maxHours, $now) {
            $totalTickets = $service->tickets->count();
            if ($totalTickets > 0) {
                $overdueTickets = $service->tickets->filter(function ($ticket) use ($maxHours, $now) {
                    $isDone = in_array(strtoupper($ticket->status), ['RESOLVED', 'COMPLETED', 'CLOSED']);
                    if ($isDone) return false;
                    
                    $deadline = Carbon::parse($ticket->created_at)->addHours($maxHours);
                    return $now->greaterThan($deadline);
                })->count();

                $kepatuhan = round((($totalTickets - $overdueTickets) / $totalTickets) * 100, 1);
            } else {
                $kepatuhan = 100.0;
            }

            $service->total_tickets = $totalTickets;
            $service->kepatuhan_sla = $kepatuhan;
            return $service;
        });

        return view('admin.evaluasi-pdf', compact('services', 'targetMinimal'));
    }
}