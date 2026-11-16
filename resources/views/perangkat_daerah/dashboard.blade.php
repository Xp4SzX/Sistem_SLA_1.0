<x-perangkat-daerah-layout>
    <x-slot name="title">Dashboard Perangkat Daerah - Sistem SLA</x-slot>
    <x-slot name="headerTitle">Dashboard Instansi</x-slot>

    <!-- LOAD CHART.JS & SCRIPT ASSETS -->
    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/js/admin/dashboard.js'])
        <script>
            window.rekapTicketData = {!! json_encode($rekapData ?? array_fill(0, 12, 0)) !!};
            window.prioritasValuesData = {!! json_encode($prioritasValues ?? [0, 0, 0, 0]) !!};
        </script>
    </x-slot>

    <!-- Welcome Banner -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-800">Selamat datang, {{ auth()->user()->name ?? 'Admin Dinas' }}!</h1>
        <p class="text-sm text-slate-500 mt-1">Berikut ringkasan laporan dan status layanan di instansi Anda</p>
    </div>

    <!-- Top Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Total Ticket</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $totalTicket ?? 0 }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Dalam Proses</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $dalamProses ?? 0 }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Selesai</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $selesai ?? 0 }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Mendekati SLA</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $mendekatiSla ?? 0 }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Melewati SLA</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $melewatiSla ?? 0 }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Kepatuhan SLA</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $kepatuhanSla ?? '0%' }}</h3>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4">Rekap Ticket Instansi</h3>
            <div class="h-64"><canvas id="rekapTicketChart"></canvas></div>
        </div>
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <h3 class="font-bold text-slate-800 mb-2">Distribusi Prioritas Tiket</h3>
            <div class="flex items-center justify-between gap-4 h-56">
                <div class="w-1/2 h-full flex items-center justify-center"><canvas id="prioritasChart"></canvas></div>
                <div class="w-1/2 space-y-3 text-sm font-medium">
                    <div class="flex justify-between items-center"><span class="text-slate-500 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Urgent</span><span class="font-bold text-slate-700">{{ $prioritas['Urgent'] ?? '0%' }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-slate-500 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> High</span><span class="font-bold text-slate-700">{{ $prioritas['High'] ?? '0%' }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-slate-500 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Medium</span><span class="font-bold text-slate-700">{{ $prioritas['Medium'] ?? '0%' }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-slate-500 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Low</span><span class="font-bold text-slate-700">{{ $prioritas['Low'] ?? '0%' }}</span></div>
                </div>
            </div>
        </div>
    </div>
</x-perangkat-daerah-layout>