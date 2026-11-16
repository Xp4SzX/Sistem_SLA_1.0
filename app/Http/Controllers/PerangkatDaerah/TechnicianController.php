<?php

namespace App\Http\Controllers\PerangkatDaerah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DinasAdmin;

class TechnicianController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        
        // Ambil nama dinas dari tabel dinas_admins, jika tidak ada gunakan nama user yang login
        $dinasAdmin = DinasAdmin::where('email', $admin->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : $admin->name;

        // Ambil teknisi yang terdaftar di dinas yang sama
        $technicians = User::where('role', 'teknisi')
            ->when($departmentName, function($q) use ($departmentName) {
                return $q->whereRaw('LOWER(department_name) = ?', [strtolower(trim($departmentName))]);
            })
            ->latest()
            ->get();

        return view('perangkat_daerah.kelolateknisi', compact('technicians'));
    }

    public function store(Request $request)
    {
        $admin = Auth::user();

        // Ambil nama dinas secara pasti
        $dinasAdmin = DinasAdmin::where('email', $admin->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : ($admin->name ?? 'Dinas Satwa');

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'whatsapp' => 'required|string|max:20',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'whatsapp_number' => $request->whatsapp,
            'identity_number' => 'TECH-' . uniqid(), 
            'password'        => Hash::make($request->password),
            'role'            => 'teknisi', 
            'department_name' => $departmentName, // Dipastikan terisi nama dinas yang valid
        ]);

        return redirect()->route('perangkat-daerah.teknisi.index')->with('success', 'Akun teknisi berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $admin = Auth::user();
        
        $dinasAdmin = DinasAdmin::where('email', $admin->email)->first();
        $departmentName = $dinasAdmin ? $dinasAdmin->nama_dinas : $admin->name;
        
        $technician = User::where('role', 'teknisi')
            ->when($departmentName, function($q) use ($departmentName) {
                return $q->whereRaw('LOWER(department_name) = ?', [strtolower(trim($departmentName))]);
            })
            ->findOrFail($id);

        $technician->delete();

        return redirect()->route('perangkat-daerah.teknisi.index')->with('success', 'Akun teknisi berhasil dihapus.');
    }
}