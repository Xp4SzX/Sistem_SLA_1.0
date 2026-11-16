<?php

namespace App\Http\Controllers;

use App\Models\DinasAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DinasAdminController extends Controller
{
    public function index()
    {
        $dinasAdmins = DinasAdmin::latest()->get();
        return view('admin.kelola-admin', compact('dinasAdmins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_dinas'       => 'required|string|max:255',
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'whatsapp'         => 'required|string|max:20',
            'penanggung_jawab' => 'required|string|max:255',
            'password'         => 'required|min:6',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat User untuk Login Utama (Role: admin_dinas)
            // Menggunakan identity_number dinamis agar unik dan tidak duplikat di database PostgreSQL
            User::create([
                'name'            => $request->name,
                'email'           => $request->email,
                'password'        => Hash::make($request->password),
                'role'            => 'admin_dinas',
                'identity_number' => 'DINAS-' . time() . '-' . rand(100, 999), 
                'whatsapp_number' => $request->whatsapp,
            ]);

            // 2. Simpan ke tabel profil dinas
            DinasAdmin::create([
                'nama_dinas'       => $request->nama_dinas,
                'name'             => $request->name,
                'email'            => $request->email,
                'whatsapp'         => $request->whatsapp,
                'penanggung_jawab' => $request->penanggung_jawab,
                'password'         => Hash::make($request->password),
            ]);
        });

        return redirect()->route('admin.dinas.index')->with('success', 'Akun admin dinas berhasil ditambahkan dan dapat login.');
    }

    public function update(Request $request, $id)
    {
        $dinasAdmin = DinasAdmin::findOrFail($id);
        $user = User::where('email', $dinasAdmin->email)->first();

        $request->validate([
            'nama_dinas'       => 'required|string|max:255',
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . ($user ? $user->id : ''),
            'whatsapp'         => 'required|string|max:20',
            'penanggung_jawab' => 'required|string|max:255',
            'password'         => 'nullable|min:6',
        ]);

        DB::transaction(function () use ($request, $dinasAdmin, $user) {
            $dataToUpdate = [
                'nama_dinas'       => $request->nama_dinas,
                'name'             => $request->name,
                'email'            => $request->email,
                'whatsapp'         => $request->whatsapp,
                'penanggung_jawab' => $request->penanggung_jawab,
            ];

            if ($request->filled('password')) {
                $dataToUpdate['password'] = Hash::make($request->password);
            }

            // Update tabel dinas_admins
            $dinasAdmin->update($dataToUpdate);

            // Update tabel users
            if ($user) {
                $userData = [
                    'name'            => $request->name,
                    'email'           => $request->email,
                    'whatsapp_number' => $request->whatsapp,
                ];
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                $user->update($userData);
            }
        });

        return redirect()->route('admin.dinas.index')->with('success', 'Data admin dinas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dinasAdmin = DinasAdmin::findOrFail($id);
        $user = User::where('email', $dinasAdmin->email)->first();

        DB::transaction(function () use ($dinasAdmin, $user) {
            if ($user) {
                $user->delete();
            }
            $dinasAdmin->delete();
        });

        return redirect()->route('admin.dinas.index')->with('success', 'Akun admin dinas berhasil dihapus.');
    }
}