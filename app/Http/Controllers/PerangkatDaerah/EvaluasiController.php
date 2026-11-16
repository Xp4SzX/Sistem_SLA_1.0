<?php

namespace App\Http\Controllers\PerangkatDaerah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\DinasAdmin;
use Carbon\Carbon;

class EvaluasiController extends Controller
{
    /**
     * Tampilan Halaman Evaluasi Dinas
     */
    public function index()
    {
        $admin = Auth::user();
        $targetMinimal = 99.9; // Target standar minimal SLA dalam persen
        $maxHours = 8; // Batas jam standar SLA (atau sesuaikan)
        $now = Carbon::now();

        // Ambil nama dinas admin yang sedang login
        $dinasAdmin = DinasAdmin::where('email', $admin->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : ($admin->name ?? 'Pemerintah Daerah');

        // Ambil layanan digital khusus dinas yang sedang login beserta relasi tiketnya
        $services = Service::whereRaw('LOWER(department_name) = ?', [strtolower(trim($departmentName))])
            ->with(['tickets'])
            ->get()
            ->map(function ($service) use ($maxHours, $now) {
                $totalTickets = $service->tickets->count();

                if ($totalTickets > 0) {
                    $overdueTickets = $service->tickets
                        ->filter(function ($ticket) use ($maxHours, $now) {
                            $isDone = in_array(
                                strtoupper($ticket->status),
                                ['RESOLVED', 'COMPLETED', 'CLOSED']
                            );

                            if ($isDone) {
                                return false;
                            }

                            $deadline = Carbon::parse($ticket->created_at)->addHours($maxHours);
                            return $now->greaterThan($deadline);
                        })
                        ->count();

                    // Kalkulasi kepatuhan persentase secara akurat
                    $kepatuhan = round((($totalTickets - $overdueTickets) / $totalTickets) * 100);
                } else {
                    $kepatuhan = 100; // Jika belum ada tiket, dianggap 100% patuh
                }

                $service->total_tickets = $totalTickets;
                $service->kepatuhan_sla = $kepatuhan;
                return $service;
            });

        // Statistik ringkasan evaluasi
        $totalLayanan = $services->count();
        
        // Layanan dianggap memenuhi target jika kepatuhan SLA >= target minimal (misal 99.9% atau 95%)
        $layananTerpenuhi = $services->where('kepatuhan_sla', '>=', 95)->count();
        $layananPerluEvaluasi = $totalLayanan - $layananTerpenuhi;

        // Titik Koordinat Grafik SVG Tren (12 Bulan / Jan - Dec)
        $targetPoints = "0,30 63,30 127,30 190,30 254,30 318,30 381,30 445,30 509,30 572,30 636,30 700,30";
        $chartPoints  = "0,50 63,40 127,60 190,45 254,30 318,55 381,40 445,35 509,50 572,40 636,30 700,25";

        return view('perangkat_daerah.evaluasi', compact(
            'services',
            'targetMinimal',
            'totalLayanan',
            'layananTerpenuhi',
            'layananPerluEvaluasi',
            'departmentName',
            'targetPoints',
            'chartPoints'
        ));
    }

    /**
     * Cetak Laporan Evaluasi khusus Dinas yang Login
     */
    public function printReport()
    {
        $admin = Auth::user();
        $targetMinimal = 99.9;
        $maxHours = 8;
        $now = Carbon::now();

        $dinasAdmin = DinasAdmin::where('email', $admin->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : ($admin->name ?? 'Pemerintah Daerah');

        $services = Service::whereRaw('LOWER(department_name) = ?', [strtolower(trim($departmentName))])
            ->with(['tickets'])
            ->get()
            ->map(function ($service) use ($maxHours, $now) {
                $totalTickets = $service->tickets->count();
                if ($totalTickets > 0) {
                    $overdueTickets = $service->tickets->filter(function ($ticket) use ($maxHours, $now) {
                        $isDone = in_array(strtoupper($ticket->status), ['RESOLVED', 'COMPLETED', 'CLOSED']);
                        if ($isDone) return false;
                        $deadline = Carbon::parse($ticket->created_at)->addHours($maxHours);
                        return $now->greaterThan($deadline);
                    })->count();
                    $kepatuhan = round((($totalTickets - $overdueTickets) / $totalTickets) * 100);
                } else {
                    $kepatuhan = 100;
                }
                $service->total_tickets = $totalTickets;
                $service->kepatuhan_sla = $kepatuhan;
                return $service;
            });

        return view('perangkat_daerah.evaluasi_cetak', compact('services', 'targetMinimal', 'admin', 'departmentName'));
    }
}