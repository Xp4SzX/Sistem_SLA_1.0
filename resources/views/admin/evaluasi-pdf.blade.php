<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Evaluasi & Kepatuhan SLA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans antialiased text-slate-800 p-8">

    <!-- HEADER LAPORAN -->
    <div class="border-b border-slate-200 pb-4 mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-lg font-bold text-slate-900 tracking-wide">LAPORAN EVALUASI & KEPATUHAN SLA</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Sistem Dukungan Pengguna & Monitoring SLA Pemerintah Daerah | Target Minimal: <span class="font-bold text-amber-600">{{ $targetMinimal ?? 99.9 }}%</span>
            </p>
        </div>
        <div class="text-right text-[10px] text-slate-400">
            <p>Dicetak pada:</p>
            <p class="font-semibold text-slate-700">{{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
        </div>
    </div>

    <!-- TABEL DATA EVALUASI -->
    <div class="overflow-hidden border border-slate-200 rounded-xl shadow-sm">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <th class="py-3 px-4 text-center w-12 font-bold">No</th>
                    <th class="py-3 px-4 font-bold">Layanan Digital</th>
                    <th class="py-3 px-4 font-bold">Perangkat Daerah</th>
                    <th class="py-3 px-4 text-center font-bold">Total Tiket</th>
                    <th class="py-3 px-4 text-center font-bold">Kepatuhan SLA</th>
                    <th class="py-3 px-4 text-center font-bold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($services ?? [] as $index => $item)
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3 px-4 text-center font-medium">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 font-bold text-slate-900">{{ $item->name }}</td>
                        <td class="py-3 px-4 font-medium">{{ $item->department->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-center font-medium">{{ $item->total_tickets ?? 0 }} Tiket</td>
                        <td class="py-3 px-4 text-center font-bold text-slate-800">{{ $item->kepatuhan_sla }}%</td>
                        <td class="py-3 px-4 text-center">
                            @if($item->kepatuhan_sla >= ($targetMinimal ?? 99.9))
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
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data layanan digital terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TANDA TANGAN / FOOTER -->
    <div class="mt-12 flex justify-end">
        <div class="text-center text-xs space-y-16">
            <div>
                <p class="text-slate-500">Administrator Sistem SLA</p>
            </div>
            <div>
                <p class="font-bold text-slate-900 underline">Admin Diskominfo</p>
                <p class="text-[10px] text-slate-400">Pemerintah Daerah</p>
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