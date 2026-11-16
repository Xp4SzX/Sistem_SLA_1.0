<x-admin-layout>
    <x-slot name="title">Dashboard Admin - Sistem SLA</x-slot>
    <x-slot name="headerTitle">Dashboard Admin</x-slot>

    <!-- LOAD CHART.JS & SCRIPT ASSETS VIA SLOT SCRIPTS -->
    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/js/admin/dashboard.js'])
        <script>
            window.rekapTicketData = {!! json_encode($rekapData ?? array_fill(0, 12, 0)) !!};
            window.prioritasValuesData = {!! json_encode($prioritasValues ?? [0, 0, 0, 0]) !!};
        </script>
    </x-slot>

    <!-- Welcome Banner -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Selamat datang, {{ auth()->user()->name ?? $userName ?? 'Admin' }}!</h1>
        <p class="text-sm text-slate-500">Berikut ringkasan laporan dan status layanan anda</p>
    </div>

    <!-- Top Metric Cards (6 Cards Layout) -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Total Ticket</p>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl font-bold text-slate-800">{{ $totalTicket ?? 0 }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Dalam Proses</p>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl font-bold text-slate-800">{{ $dalamProses ?? 0 }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Selesai</p>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl font-bold text-slate-800">{{ $selesai ?? 0 }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Mendekati SLA</p>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl font-bold text-slate-800">{{ $mendekatiSla ?? 0 }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Melewati SLA</p>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl font-bold text-slate-800">{{ $melewatiSla ?? 0 }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Kepatuhan SLA</p>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl font-bold text-slate-800">{{ $kepatuhanSla ?? '0%' }}</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Rekap Ticket (Line Chart) -->
        <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4">Rekap Ticket</h3>
            <div class="h-64">
                <canvas id="rekapTicketChart"></canvas>
            </div>
        </div>

        <!-- Distribusi Ticket Berdasarkan Prioritas (Doughnut Chart) -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <h3 class="font-bold text-slate-800 mb-2">Distribusi Ticket Berdasarkan Prioritas</h3>
            <div class="flex items-center justify-between gap-4 h-56">
                <div class="w-1/2 h-full flex items-center justify-center">
                    <canvas id="prioritasChart"></canvas>
                </div>
                <div class="w-1/2 space-y-3 text-sm font-medium">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Urgent</span>
                        <span class="font-bold text-slate-700">{{ $prioritas['Urgent'] ?? '0%' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> High</span>
                        <span class="font-bold text-slate-700">{{ $prioritas['High'] ?? '0%' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Medium</span>
                        <span class="font-bold text-slate-700">{{ $prioritas['Medium'] ?? '0%' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Low</span>
                        <span class="font-bold text-slate-700">{{ $prioritas['Low'] ?? '0%' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Summary Section -->
    <div class="space-y-3">
        <h4 class="text-xs font-bold text-slate-600">Ringkasan Service Level Agreement</h4>
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 divide-y md:divide-y-0 md:divide-x divide-slate-200">
            <div class="p-4">
                <p class="text-[11px] text-slate-500 font-medium">Rata-rata respon time</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ $summarySla['avg_response'] ?? '0 mnt' }}</p>
            </div>
            <div class="p-4">
                <p class="text-[11px] text-slate-500 font-medium">Rata-rata resolution time</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ $summarySla['avg_resolution'] ?? '0 jam' }}</p>
            </div>
            <div class="p-4">
                <p class="text-[11px] text-slate-500 font-medium">Rata-rata uptime</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ $summarySla['avg_uptime'] ?? '0%' }}</p>
            </div>
            <div class="p-4">
                <p class="text-[11px] text-slate-500 font-medium">Total pelanggaran SLA</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ $summarySla['total_pelanggaran'] ?? 0 }}</p>
            </div>
            <div class="p-4">
                <p class="text-[11px] text-slate-500 font-medium">Tingkat kepatuhan SLA</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ $summarySla['kepatuhan_sla'] ?? '0%' }}</p>
            </div>
            <div class="p-4">
                <p class="text-[11px] text-slate-500 font-medium">Total layanan ditinjau</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ $summarySla['total_layanan'] ?? 0 }}</p>
            </div>
        </div>
    </div>

</x-admin-layout>