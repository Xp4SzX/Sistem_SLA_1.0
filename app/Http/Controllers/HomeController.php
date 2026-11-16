<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Service;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Ambil Statistik Pengaduan & Hitung SLA dari Database
        $totalPengaduan = Ticket::count();
        $dalamProses    = Ticket::whereIn('status', ['submitted', 'verified', 'in_progress'])->count();
        $selesai        = Ticket::whereIn('status', ['resolved', 'closed', 'completed'])->count();

        $maxHours = 8;
        $now = Carbon::now();
        $overdueCount = 0;

        foreach (Ticket::all() as $t) {
            $isDone = in_array(strtolower($t->status), ['resolved', 'completed', 'closed']);
            if ($isDone) continue;

            $deadline = Carbon::parse($t->created_at)->addHours($maxHours);
            if ($now->greaterThan($deadline)) {
                $overdueCount++;
            }
        }

        $capaianSla = $totalPengaduan > 0 
            ? round((($totalPengaduan - $overdueCount) / $totalPengaduan) * 100, 1) . '%' 
            : '100%';

        // 2. Ambil Daftar Layanan Digital berdasarkan is_active = true dari Database
        $services = Service::where('is_active', true)->latest()->get();

        // Mengembalikan ke view 'home' (home.blade.php)
        return view('home', compact(
            'totalPengaduan',
            'dalamProses',
            'selesai',
            'capaianSla',
            'services'
        ));
    }
}