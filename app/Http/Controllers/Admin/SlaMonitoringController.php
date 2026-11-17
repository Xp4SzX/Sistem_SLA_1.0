<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DinasAdmin;
use App\Models\Service;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class SlaMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'department_id' => ['nullable', 'integer', 'exists:dinas_admins,id'],
            'service_status' => ['nullable', 'in:active,maintenance'],
        ]);

        $departments = DinasAdmin::select('id', 'nama_dinas as name', 'nama_dinas')
            ->orderBy('nama_dinas')
            ->get();
        $selectedDepartment = $departments->firstWhere('id', (int) ($validated['department_id'] ?? 0));
        $serviceStatus = $validated['service_status'] ?? null;

        $serviceFilters = function ($query) use ($selectedDepartment, $serviceStatus) {
            if ($selectedDepartment) {
                $query->where('department_name', $selectedDepartment->nama_dinas);
            }

            if ($serviceStatus) {
                $query->where('status', $serviceStatus);
            }
        };

        $services = Service::query()
            ->withCount('tickets')
            ->where($serviceFilters)
            ->orderBy('name')
            ->get();

        $tickets = Ticket::query()
            ->whereHas('service', $serviceFilters)
            ->latest()
            ->get();

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

        $complianceRate = $totalTickets > 0
            ? round((($totalTickets - $overdueCount) / $totalTickets) * 100, 1)
            : 100;

        $kpi = [
            'services'   => $services->where('status', '!=', 'disabled')->count(),
            'tickets'    => $totalTickets,
            'on_track'   => $onTrackCount,
            'warning'    => $warningCount,
            'overdue'    => $overdueCount,
            'compliance' => $complianceRate,
        ];

        $monthlyTickets = $tickets
            ->filter(fn ($ticket) => Carbon::parse($ticket->created_at)->year === $now->year)
            ->groupBy(fn ($ticket) => Carbon::parse($ticket->created_at)->month)
            ->map->count()
            ->all();

        $chartMonthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartMonthlyData[] = $monthlyTickets[$m] ?? 0;
        }

        return view('admin.sla-monitoring', compact(
            'tickets',
            'services',
            'departments',
            'kpi',
            'chartMonthlyData'
        ));
    }
}