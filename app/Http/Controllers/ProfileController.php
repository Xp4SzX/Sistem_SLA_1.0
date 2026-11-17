<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('pelapor.profile', compact('user'));
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
            'department_name' => ['nullable', 'string', 'max:150'],
        ], [
            'name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'whatsapp_number.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp_number' => $validated['whatsapp_number'],
            'department_name' => $validated['department_name'] ?? null,
        ]);

        return redirect()
            ->route('profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}