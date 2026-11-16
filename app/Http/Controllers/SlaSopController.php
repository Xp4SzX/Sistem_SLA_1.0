<?php

namespace App\Http\Controllers;

use App\Models\Service;

class SlaSopController extends Controller
{
    public function index()
    {
        $services = Service::with(['department', 'sla'])
            ->where('status', '!=', 'disabled')
            ->orderBy('name')
            ->get();

        return view('pelapor.sla-sop', compact('services'));
    }
}