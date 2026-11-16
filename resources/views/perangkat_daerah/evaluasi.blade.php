<x-perangkat-daerah-layout>
    <x-slot name="title">Evaluasi & Kepatuhan SLA - Perangkat Daerah</x-slot>
    <x-slot name="headerTitle">Evaluasi & Kepatuhan SLA Dinas</x-slot>

    <div class="p-6 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Laporan Evaluasi Internal Dinas</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Monitoring performa layanan digital dan tingkat kepatuhan SLA pada instansi 
                    <span class="font-bold text-emerald-800">{{ Auth::user()->department->name ?? 'Perangkat Daerah' }}</span>.
                </p>
            </div>
            <div>
                <a href="{{ route('perangkat-daerah.evaluasi.cetak') }}" target="_blank" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#052e23] hover:bg-emerald-900 text-amber-400 font-bold text-xs rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Laporan Evaluasi</span>
                </a>
            </div>
        </div>

        <!-- Metric Cards khusus Dinas -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 text-[#052e23] rounded-xl flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-cubes"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Layanan Dinas Ini</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $totalLayanan }}</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center text-xl font-bold">
                    <i class="fa-regular fa-circle-check"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Memenuhi Target SLA</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $layananTerpenuhi }}</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Perlu Evaluasi SLA</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $layananPerluEvaluasi }}</h3>
                </div>
            </div>
        </div>

        <!-- Tabel Evaluasi Layanan Dinas -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-3">Daftar Kepatuhan SLA Layanan</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                            <th class="py-3 px-4 text-center w-12 font-bold">No</th>
                            <th class="py-3 px-4 font-bold">Nama Layanan Digital</th>
                            <th class="py-3 px-4 text-center font-bold">Total Tiket Masuk</th>
                            <th class="py-3 px-4 text-center font-bold">Kepatuhan SLA</th>
                            <th class="py-3 px-4 text-center font-bold">Status Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($services as $index => $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 text-center font-medium">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $item->name }}</td>
                                <td class="py-3 px-4 text-center font-medium">{{ $item->total_tickets ?? 0 }} Tiket</td>
                                <td class="py-3 px-4 text-center font-bold text-slate-800">{{ $item->kepatuhan_sla }}%</td>
                                <td class="py-3 px-4 text-center">
                                    @if($item->kepatuhan_sla >= $targetMinimal)
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-semibold text-[10px]">Terpenuhi</span>
                                    @elseif($item->kepatuhan_sla >= 85)
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-semibold text-[10px]">Mendekati</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full font-semibold text-[10px]">Terlewati</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data layanan digital untuk dinas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-perangkat-daerah-layout>