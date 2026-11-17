<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SlaSopController extends Controller
{
    public function index()
    {
        $services = Service::with('sla')
            ->where('status', '!=', 'disabled')
            ->orderBy('name')
            ->get();

        return view('pelapor.sla-sop', compact('services'));
    }

    public function download(Service $service): StreamedResponse
    {
        abort_unless($service->sop_file, 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($service->sop_file), 404);

        $extension = pathinfo($service->sop_file, PATHINFO_EXTENSION);
        $fileName = Str::slug($service->name).'-SOP'.($extension ? '.'.$extension : '');

        return $disk->download($service->sop_file, $fileName);
    }
}