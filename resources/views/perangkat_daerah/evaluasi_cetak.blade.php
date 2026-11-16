<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Evaluasi SLA - {{ $admin->department->name ?? 'Dinas' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans antialiased text-slate-800 p-8">

    <!-- HEADER LAPORAN DINAS -->
    <div class="border-b-2 border-slate-900 pb-4 mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-wide uppercase">LAPORAN EVALUASI & KEPATUHAN SLA LAYANAN DIGITAL</h1>
            <h2 class="text-sm font-semibold text-emerald-800 uppercase">{{ $admin->department->name ?? 'PERANGKAT DAERAH' }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Target Minimal SLA: <span class="font-bold text-amber-600">{{ $targetMinimal }}%</span>
            </p>
        </div>
        <div class="text-right text-[10px] text-slate-400">
            <p>Tanggal Cetak:</p>
            <p class="font-semibold text-slate-700">{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>
    </div>

    <!-- TABEL DATA EVALUASI LAYANAN DINAS -->
    <div class="overflow-hidden border border-slate-200 rounded-xl shadow-sm">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-slate-100 text-slate-700 border-b border-slate-200">
                    <th class="py-3 px-4 text-center w-12 font-bold">No</th>
                    <th class="py-3 px-4 font-bold">Layanan Digital</th>
                    <th class="py-3 px-4 text-center font-bold">Total Tiket Keluhan</th>
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
                        <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data layanan digital terdaftar pada dinas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TANDA TANGAN KEPALA / ADMIN DINAS -->
    <div class="mt-12 flex justify-end">
        <div class="text-center text-xs space-y-16">
            <div>
                <p class="text-slate-500">Admin Perangkat Daerah,</p>
                <p class="font-semibold text-slate-700">{{ $admin->department->name ?? 'Perangkat Daerah' }}</p>
            </div>
            <div>
                <p class="font-bold text-slate-900 underline">{{ $admin->name }}</p>
                <p class="text-[10px] text-slate-400">NIP / WhatsApp: {{ $admin->identity_number ?? $admin->whatsapp_number ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- SCRIPT OTOMATIS CETAK PDF -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>