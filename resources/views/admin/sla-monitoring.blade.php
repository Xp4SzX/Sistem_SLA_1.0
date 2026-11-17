<x-admin-layout>
    <x-slot name="title">SLA Monitoring - Admin Sistem SLA</x-slot>
    <x-slot name="headerTitle">Monitoring SLA</x-slot>

    <!-- LOAD CHART.JS & SCRIPT ASSETS VIA SLOT SCRIPTS -->
    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/js/admin/slamonitoring.js'])
        <script>
            window.kpiData = @json($kpi ?? []);
            window.monthlyData = @json($chartMonthlyData ?? []);
        </script>
    </x-slot>

    <!-- MAIN BODY CONTENT -->
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-700">Dashboard</a>
            <span>&gt;</span>
            <span class="font-semibold text-slate-800">SLA Monitoring</span>
        </div>

        <!-- Top Summary Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-slate-100 rounded-lg text-slate-600"><i class="fa-regular fa-file-lines text-lg"></i></div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Total Layanan</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $kpi['services'] ?? 0 }}</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-emerald-50 rounded-lg text-emerald-600"><i class="fa-regular fa-chart-bar text-lg"></i></div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Kepatuhan SLA</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $kpi['compliance'] ?? 0 }}%</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-emerald-50 rounded-lg text-emerald-600"><i class="fa-regular fa-circle-check text-lg"></i></div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Memenuhi SLA</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $kpi['on_track'] ?? 0 }}</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-amber-50 rounded-lg text-amber-600"><i class="fa-solid fa-triangle-exclamation text-lg"></i></div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Mendekati SLA</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $kpi['warning'] ?? 0 }}</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-rose-50 rounded-lg text-rose-600"><i class="fa-regular fa-clock text-lg"></i></div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Melewati SLA</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $kpi['overdue'] ?? 0 }}</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-indigo-50 rounded-lg text-indigo-600"><i class="fa-solid fa-ticket text-lg"></i></div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Total Tiket</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $kpi['tickets'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.sla-monitoring') }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex flex-wrap items-center gap-6">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Perangkat Daerah</label>
                    <select name="department_id" onchange="this.form.submit()" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-600 focus:outline-none focus:ring-1 focus:ring-emerald-700 min-w-[200px]">
                        <option value="">Semua Perangkat</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Status Layanan</label>
                    <select name="service_status" onchange="this.form.submit()" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-600 focus:outline-none focus:ring-1 focus:ring-emerald-700 min-w-[150px]">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('service_status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="maintenance" {{ request('service_status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
            </div>

            @if(request('department_id') || request('service_status'))
                <a href="{{ route('admin.sla-monitoring') }}" class="text-rose-600 hover:underline font-semibold">Reset Filter</a>
            @endif
        </form>

        <!-- Visualizations Row -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-2 text-sm">Tren Volume Tiket {{ date('Y') }}</h3>
                <div class="h-64">
                    <canvas id="slaBarChart"></canvas>
                </div>
            </div>

            <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <h3 class="font-bold text-slate-800 mb-2 text-sm">Status Kepatuhan SLA</h3>
                <div class="flex items-center justify-between gap-4 h-56">
                    <div class="w-1/2 h-full flex items-center justify-center">
                        <canvas id="statusKepatuhanChart"></canvas>
                    </div>
                    <div class="w-1/2 space-y-3 text-xs font-medium">
                        <div class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#052e23]"></span>
                            <span>Memenuhi ({{ $kpi['on_track'] ?? 0 }})</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>Mendekati ({{ $kpi['warning'] ?? 0 }})</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span>Melewati ({{ $kpi['overdue'] ?? 0 }} Tiket / {{ $kpi['compliance'] ?? 0 }}%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Ringkasan Layanan Digital -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <h4 class="font-bold text-slate-800 text-sm">Ringkasan Layanan Digital</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-slate-600 text-xs font-semibold border-b border-slate-200">
                            <th class="py-3 px-4 text-center">No</th>
                            <th class="py-3 px-4">Layanan Digital</th>
                            <th class="py-3 px-4">Perangkat Daerah</th>
                            <th class="py-3 px-4 text-center">Total Tiket</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @forelse($services as $index => $service)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 text-center">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-bold text-slate-800">{{ $service->name }}</td>
                                <td class="py-3 px-4">{{ $service->department_name ?: 'Pemerintah Daerah' }}</td>
                                <td class="py-3 px-4 text-center font-bold">{{ $service->tickets_count }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-1 {{ $service->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($service->status === 'disabled' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-800') }} font-semibold text-[10px] rounded-full">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('admin.ticketing', ['service_id' => $service->id]) }}" class="px-2.5 py-1 bg-[#052e23] hover:bg-emerald-900 text-white rounded font-medium text-[10px] transition inline-block">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-slate-500">Belum ada data layanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>