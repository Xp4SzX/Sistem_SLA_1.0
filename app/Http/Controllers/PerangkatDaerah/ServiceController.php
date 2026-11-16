<?php

namespace App\Http\Controllers\PerangkatDaerah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\DinasAdmin;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil nama dinas admin yang sedang login
        $dinasAdmin = DinasAdmin::where('email', $user->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : ($user->name ?? 'Pemerintah Daerah');

        // FILTER: Hanya ambil layanan digital yang sesuai dengan perangkat daerah yang login
        $services = Service::where('department_name', $departmentName)
            ->latest()
            ->get();

        return view('perangkat_daerah.layanandigital', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'service_code' => 'required|string|max:50',
            'type'         => 'required|string',
            'status'       => 'required|in:active,maintenance,gangguan',
            'menimbang'    => 'nullable|string',
            'mengingat'    => 'nullable|string',
        ]);

        $user = Auth::user();
        $dinasAdmin = DinasAdmin::where('email', $user->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : ($user->name ?? 'Pemerintah Daerah');

        $data = $request->all();
        $data['department_name'] = $departmentName;
        
        // Nilai default untuk menghindari error Not Null Violation di database
        $data['response_time'] = $request->response_time ?? '30 menit';
        $data['resolution_time'] = $request->resolution_time ?? '8 jam';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        if ($request->hasFile('sop_file')) {
            $data['sop_file'] = $request->file('sop_file')->store('sop_files', 'public');
        }

        Service::create($data);

        return redirect()->route('perangkat-daerah.layanandigital')->with('success', 'Layanan digital berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'service_code' => 'required|string|max:50',
            'type'         => 'required|string',
            'status'       => 'required|in:active,maintenance,gangguan',
            'menimbang'    => 'nullable|string',
            'mengingat'    => 'nullable|string',
        ]);

        $user = Auth::user();
        $dinasAdmin = DinasAdmin::where('email', $user->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : ($user->name ?? 'Pemerintah Daerah');

        $data = $request->all();
        $data['department_name'] = $departmentName;

        $data['response_time'] = $request->response_time ?? '30 menit';
        $data['resolution_time'] = $request->resolution_time ?? '8 jam';

        if ($request->hasFile('image')) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        if ($request->hasFile('sop_file')) {
            if ($service->sop_file && Storage::disk('public')->exists($service->sop_file)) {
                Storage::disk('public')->delete($service->sop_file);
            }
            $data['sop_file'] = $request->file('sop_file')->store('sop_files', 'public');
        }

        $service->update($data);

        return redirect()->route('perangkat-daerah.layanandigital')->with('success', 'Layanan digital berhasil diperbarui.');
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

        return redirect()->route('perangkat-daerah.layanandigital')->with('success', 'Layanan digital berhasil dihapus.');
    }
}