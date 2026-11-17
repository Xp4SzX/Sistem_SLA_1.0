<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\DinasAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LayananDigitalController extends Controller
{
    public function index()
    {
        // Mengambil layanan beserta tiketnya (tanpa relasi department tabel lama)
        $services = Service::with(['tickets'])->latest()->get();
        $maxHours = 8; // Standar SLA disamakan menjadi 8 Jam
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

                $kepatuhan = round((($totalTickets - $overdueTickets) / $totalTickets) * 100);
            } else {
                $kepatuhan = 100;
            }

            $service->total_tickets = $totalTickets;
            $service->kepatuhan_sla = $kepatuhan;
            return $service;
        });

        $totalLayanan = $services->count();
        $layananAktif = $services->where('status', 'active')->count();
        
        // Mengambil daftar nama dinas untuk dropdown dari tabel dinas_admins
        $departments = DinasAdmin::select('id', 'nama_dinas as name')->orderBy('nama_dinas')->get();

        return view('admin.layanandigital', compact('services', 'totalLayanan', 'layananAktif', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:100',
            'service_code'    => 'required|string|max:20|unique:services,service_code',
            'department_name' => 'required|string|max:255',
            'type'            => 'required|in:pelayanan_publik,administrasi_pemerintahan',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|max:5120',
            'sop_file'        => 'nullable|mimes:pdf,doc,docx|max:15360',
            'response_time'   => 'nullable|string|max:50',
            'resolution_time' => 'nullable|string|max:50',
            'valid_from'      => 'nullable|date',
            'valid_until'     => 'nullable|date',
        ]);

        Storage::disk('public')->makeDirectory('services');
        Storage::disk('public')->makeDirectory('sop_files');

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $sopPath = null;
        if ($request->hasFile('sop_file')) {
            $sopPath = $request->file('sop_file')->store('sop_files', 'public');
        }

        // Menyimpan data layanan dengan department_name berupa teks langsung
        Service::create([
            'name'            => $request->name,
            'service_code'    => strtoupper($request->service_code),
            'department_name' => $request->department_name,
            'type'            => $request->type,
            'description'     => $request->description,
            'image'           => $imagePath,
            'sop_file'        => $sopPath,
            'response_time'   => $request->response_time ?? '30 menit',
            'resolution_time' => $request->resolution_time ?? '8 jam',
            'valid_from'      => $request->valid_from,
            'valid_until'     => $request->valid_until,
            'status'          => 'active',
            'current_uptime'  => 100.00,
        ]);

        return redirect()->back()->with('success', 'Layanan digital dan dokumen SOP berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:100',
            'service_code'    => 'required|string|max:20|unique:services,service_code,' . $id,
            'department_name' => 'required|string|max:255',
            'type'            => 'required|in:pelayanan_publik,administrasi_pemerintahan',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|max:5120',
            'sop_file'        => 'nullable|mimes:pdf,doc,docx|max:15360',
            'response_time'   => 'nullable|string|max:50',
            'resolution_time' => 'nullable|string|max:50',
            'valid_from'      => 'nullable|date',
            'valid_until'     => 'nullable|date',
        ]);

        Storage::disk('public')->makeDirectory('services');
        Storage::disk('public')->makeDirectory('sop_files');

        $imagePath = $service->image;
        if ($request->hasFile('image')) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $sopPath = $service->sop_file;
        if ($request->hasFile('sop_file')) {
            if ($service->sop_file && Storage::disk('public')->exists($service->sop_file)) {
                Storage::disk('public')->delete($service->sop_file);
            }
            $sopPath = $request->file('sop_file')->store('sop_files', 'public');
        }

        $service->update([
            'name'            => $request->name,
            'service_code'    => strtoupper($request->service_code),
            'department_name' => $request->department_name,
            'type'            => $request->type,
            'description'     => $request->description,
            'image'           => $imagePath,
            'sop_file'        => $sopPath,
            'response_time'   => $request->response_time,
            'resolution_time' => $request->resolution_time,
            'valid_from'      => $request->valid_from,
            'valid_until'     => $request->valid_until,
        ]);

        return redirect()->back()->with('success', 'Data layanan digital dan dokumen SOP berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        if ($service->sop_file && Storage::disk('public')->exists($service->sop_file)) {
            Storage::disk('public')->delete($service->sop_file);
        }

        $service->delete();

        return redirect()->back()->with('success', 'Data layanan digital berhasil dihapus!');
    }
}