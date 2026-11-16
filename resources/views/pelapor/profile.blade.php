@php
    $user = Auth::user();
@endphp

<x-app-layout>
    <x-slot name="title">Profil Pengguna - SLA Helpdesk</x-slot>
    <x-slot name="headerTitle">Profil Pengguna</x-slot>

    <div class="max-w-6xl mx-auto space-y-6">

        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-semibold">
                <i class="fa-solid fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-semibold">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-32 bg-gradient-to-r from-[#022C22] to-emerald-700"></div>

            <div class="px-6 pb-6">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12">
                    <div class="w-24 h-24 rounded-full bg-[#0F172A] border-4 border-white text-white flex items-center justify-center text-2xl font-bold shadow-lg">
                        {{ strtoupper(substr($user->name ?? 'User', 0, 2)) }}
                    </div>

                    <div class="flex-1">
                        <h1 class="text-xl font-extrabold text-slate-800">
                            {{ $user->name }}
                        </h1>

                        <p class="text-sm text-slate-500">
                            {{ $user->email }}
                        </p>
                    </div>

                    <span class="self-start sm:self-auto px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase">
                        {{ $user->role ?? 'Pengguna' }}
                    </span>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <section class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-800">
                            Edit Informasi Akun
                        </h2>

                        <p class="text-xs text-slate-500 mt-1">
                            Perbarui informasi kontak akun Anda.
                        </p>
                    </div>

                    <i class="fa-solid fa-user-gear text-xl text-emerald-600"></i>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            NIK
                        </label>

                        <input
                            type="text"
                            value="{{ $user->identity_number }}"
                            disabled
                            class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-500 cursor-not-allowed"
                        >

                        <p class="mt-1 text-[11px] text-slate-400">
                            NIK tidak dapat diubah.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            Nomor WhatsApp
                        </label>

                        <input
                            type="text"
                            name="whatsapp_number"
                            value="{{ old('whatsapp_number', $user->whatsapp_number) }}"
                            required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                        >
                    </div>

<div>
    <label for="department_name" class="block text-xs font-bold text-slate-600 mb-1.5">
        Departemen / OPD <span class="text-slate-400 font-normal">(Opsional)</span>
    </label>

    <input
        id="department_name"
        type="text"
        name="department_name"
        value="{{ old('department_name', $user->department->name ?? '') }}"
        placeholder="Contoh: Dinas Komunikasi dan Informatika"
        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
    >

    <p class="mt-1 text-[11px] text-slate-400">
        Kosongkan jika tidak ada, atau sesuaikan dengan data departemen yang tersedia.
    </p>

    @error('department_name')
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition"
                        >
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </section>

            <section class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>

                    <div>
                        <h2 class="font-extrabold text-slate-800">
                            Aktivitas Saya
                        </h2>

                        <p class="text-xs text-slate-500">
                            Ringkasan laporan tiket
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs font-semibold text-slate-600">
                            Total Tiket
                        </span>

                        <span class="text-lg font-extrabold text-slate-800">
                            {{ $user->tickets()->count() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl">
                        <span class="text-xs font-semibold text-blue-700">
                            Sedang Diproses
                        </span>

                        <span class="text-lg font-extrabold text-blue-700">
                            {{ $user->tickets()->whereIn('status', ['submitted', 'verified', 'in_progress'])->count() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl">
                        <span class="text-xs font-semibold text-emerald-700">
                            Selesai
                        </span>

                        <span class="text-lg font-extrabold text-emerald-700">
                            {{ $user->tickets()->whereIn('status', ['resolved', 'closed'])->count() }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('user.ticketing') }}" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                        <i class="fa-solid fa-ticket"></i>
                    Lihat Tiket Saya
                </a>
            </section>

        </div>
    </div>
</x-app-layout>