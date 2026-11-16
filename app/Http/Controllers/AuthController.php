<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Proses Registrasi User Baru (Publik / ASN)
     */
    public function registerUser(Request $request)
    {
        // 1. Validasi Input dengan batasan ketat
        $request->validate([
            'name'     => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'nik'      => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/', 'unique:users,identity_number'],
            'phone'    => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'email'    => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
        ], [
            'name.regex'  => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'nik.regex'   => 'NIK harus berupa angka.',
            'nik.size'    => 'NIK harus tepat 16 digit.',
            'phone.regex' => 'Nomor telepon/WhatsApp hanya boleh berisi angka.',
        ]);

        // 2. Simpan Data Sesuai Kolom Database
        User::create([
            'name'            => $request->name,
            'identity_number' => $request->nik,
            'whatsapp_number' => $request->phone,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'role'            => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan masuk menggunakan akun Anda.');
    }

    /**
     * Proses Login User (Publik / ASN)
     */
    public function loginUser(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string', // NIK / Email / Username
            'password' => 'required|string',
        ]);

        // Cek login via NIK (identity_number) atau Email
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'identity_number';

        if (Auth::attempt([$loginType => $request->login, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Jika yang login ternyata admin, alihkan ke dashboard admin
            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'login' => 'NIK, Email, atau Kata Sandi yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    /**
     * Proses Login Admin & Perangkat Daerah (Tim Teknis / Diskominfotik / Admin Dinas)
     */
    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'identity_number';

        if (Auth::attempt([
            $loginType => $credentials['username'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            
            $request->session()->regenerate();
            $user = Auth::user();

            // 1. Tambahkan 'teknisi' ke daftar role yang diizinkan masuk lewat halaman login admin
            $allowedRoles = ['admin', 'admin_dinas', 'teknisi'];
            
            if (!in_array($user->role, $allowedRoles)) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akses ditolak. Akun Anda tidak memiliki hak akses ke halaman ini.',
                ]);
            }

            // 2. Arahkan Redirect Berdasarkan Role Masing-Masing
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'admin_dinas') {
                return redirect()->intended(route('perangkat-daerah.dashboard'));
            } elseif ($user->role === 'teknisi') {
                // ARAH KE HALAMAN KHUSUS TEKNISI
                return redirect()->intended(route('teknisi.dashboard'));
            } else {
                return redirect()->intended(route('operator.dashboard'));
            }
        }

        return back()->withErrors([
            'username' => 'Kredensial atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    /**
     * Logout Sesi
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}