<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load('department');

        $departments = Department::orderBy('name')->get();

        return view('pelapor.profile', compact('user', 'departments'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'whatsapp_number' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            // Diubah menjadi nullable agar boleh kosong/tidak diisi
            'department_name' => ['nullable', 'string', 'max:150'],
        ], [
            'name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'whatsapp_number.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
        ]);

        $departmentId = null;

        // Cek hanya jika pengguna mengisi nama departemen
        if (!empty($validated['department_name'])) {
            $department = Department::whereRaw(
                'LOWER(name) = ?',
                [strtolower(trim($validated['department_name']))]
            )->first();

            if (!$department) {
                return back()
                    ->withErrors([
                        'department_name' => 'Departemen/OPD tidak ditemukan.',
                    ])
                    ->withInput();
            }

            $departmentId = $department->id;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp_number' => $validated['whatsapp_number'],
            // Nilai bisa berupa ID departemen yang valid atau NULL jika dikosongkan
            'department_id' => $departmentId,
        ]);

        return redirect()
            ->route('profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}