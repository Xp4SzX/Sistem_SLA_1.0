<x-admin-layout>
    <x-slot name="title">Evaluasi dan Laporan - Sistem SLA</x-slot>
    <x-slot name="headerTitle">Evaluasi dan Laporan</x-slot>

    <!-- CONTENT -->
    <div class="space-y-5">

        <!-- BREADCRUMB -->
        <div class="text-xs text-slate-500 font-medium">
            Dashboard &gt;
            <span class="text-[#052e23] font-bold">Evaluasi SLA</span>
        </div>

        <!-- KPI -->
        <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">

            <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#052e23] flex items-center justify-center">
                        <i class="fa-regular fa-file-lines"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-semibold">Total Layanan</p>
                        <p class="text-lg font-bold text-slate-800">{{ $totalLayanan ?? 0 }}</p>
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 mt-2">Semua layanan</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i class="fa-regular fa-circle-check"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-semibold">Rata-rata Kepatuhan SLA</p>
                        <p class="text-lg font-bold text-slate-800">{{ $rataKepatuhan ?? 100 }}%</p>
                    </div>
                </div>
                <p class="text-[9px] text-emerald-600 mt-2">Performa Sistem</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-semibold">SLA Terpenuhi</p>
                        <p class="text-lg font-bold text-slate-800">{{ $slaTerpenuhi ?? 0 }}</p>
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 mt-2">Dari total layanan</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-semibold">SLA Mendekati</p>
                        <p class="text-lg font-bold text-slate-800">{{ $slaMendekati ?? 0 }}</p>
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 mt-2">Hampir melewati batas</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-semibold">SLA Terlewati</p>
                        <p class="text-lg font-bold text-slate-800">{{ $slaTerlewati ?? 0 }}</p>
                    </div>
                </div>
                <p class="text-[9px] text-rose-600 mt-2">Dari total layanan</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-circle-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-semibold">Total Pelanggaran SLA</p>
                        <p class="text-lg font-bold text-slate-800">{{ $totalPelanggaran ?? 0 }}</p>
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 mt-2">Akumulasi tiket</p>
            </div>

        </section>

        <!-- FILTER -->
        <section class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('admin.evaluasi') }}" class="flex flex-wrap items-end gap-3">

                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="px-3 py-2 border border-slate-200 rounded-lg text-[11px] bg-white focus:outline-none focus:ring-1 focus:ring-[#052e23]">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="px-3 py-2 border border-slate-200 rounded-lg text-[11px] bg-white focus:outline-none focus:ring-1 focus:ring-[#052e23]">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Perangkat Daerah</label>
                    <select name="department" onchange="this.form.submit()" class="w-40 px-3 py-2 border border-slate-200 rounded-lg text-[11px] bg-white focus:outline-none focus:ring-1 focus:ring-[#052e23]">
                        <option value="Semua Perangkat">Semua Perangkat</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Status SLA</label>
                    <select name="status_sla" onchange="this.form.submit()" class="w-32 px-3 py-2 border border-slate-200 rounded-lg text-[11px] bg-white focus:outline-none focus:ring-1 focus:ring-[#052e23]">
                        <option value="Semua Status">Semua Status</option>
                        <option value="Terpenuhi" {{ request('status_sla') == 'Terpenuhi' ? 'selected' : '' }}>Terpenuhi</option>
                        <option value="Mendekati" {{ request('status_sla') == 'Mendekati' ? 'selected' : '' }}>Mendekati</option>
                        <option value="Terlewati" {{ request('status_sla') == 'Terlewati' ? 'selected' : '' }}>Terlewati</option>
                    </select>
                </div>

                @if(request('start_date') || request('end_date') || request('department') || request('status_sla'))
                    <a href="{{ route('admin.evaluasi') }}"
                       class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition flex items-center justify-center" title="Reset Filter">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif

                <a href="{{ route('admin.evaluasi.pdf') }}" target="_blank"
                   class="ml-auto px-4 py-2 bg-white border border-slate-200 rounded-lg text-[10px] font-semibold text-slate-600 hover:bg-slate-50 flex items-center gap-1 shadow-sm">
                    <i class="fa-solid fa-file-pdf text-rose-600"></i> Export
                </a>
            </form>
        </section>

        <!-- MAIN REPORT GRID -->
        <section class="grid grid-cols-1 xl:grid-cols-12 gap-5">

            <!-- TABLE -->
            <div class="xl:col-span-8 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200">
                    <h3 class="font-bold text-xs text-slate-800">Riwayat SLA per Layanan</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[10px]">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr class="border-b border-slate-200">
                                <th class="px-3 py-2 text-center">No</th>
                                <th class="px-3 py-2">Layanan Digital</th>
                                <th class="px-3 py-2">Perangkat Daerah</th>
                                <th class="px-3 py-2">Periode</th>
                                <th class="px-3 py-2">Total Tiket</th>
                                <th class="px-3 py-2">Kepatuhan SLA</th>
                                <th class="px-3 py-2 text-center">Status</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($services ?? [] as $index => $item)
                                <tr>
                                    <td class="px-3 py-3 text-center">{{ $index + 1 }}</td>
                                    <td class="px-3 py-3 font-bold text-slate-800">{{ $item->name }}</td>
                                    <td class="px-3 py-3">{{ $item->department->name ?? '-' }}</td>
                                    <td class="px-3 py-3">15 Agu - 15 Sep</td>
                                    <td class="px-3 py-3">{{ $item->total_tickets ?? 0 }} Tiket</td>
                                    <td class="px-3 py-3 font-bold {{ $item->kepatuhan_sla >= 95 ? 'text-emerald-600' : ($item->kepatuhan_sla >= 85 ? 'text-amber-600' : 'text-rose-600') }}">
                                        {{ $item->kepatuhan_sla }}%
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        @if($item->kepatuhan_sla >= 95)
                                            <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold">Terpenuhi</span>
                                        @elseif($item->kepatuhan_sla >= 85)
                                            <span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 font-bold">Mendekati</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 font-bold">Terlewati</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <button class="text-slate-500 hover:text-[#052e23]">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-6 text-center text-slate-400">Belum ada data layanan digital terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

<!-- DONUT -->
            <div class="xl:col-span-4 bg-white rounded-xl border border-slate-200 shadow-sm p-4 h-fit">
                <h3 class="font-bold text-xs text-slate-800 mb-3">Ringkasan Kepatuhan SLA</h3>

                <div class="flex items-center gap-5">
                    <div class="relative w-36 h-36 shrink-0 flex items-center justify-center">
                        <div class="absolute inset-0 bg-emerald-700 rounded-full opacity-10"></div>
                        <div class="flex flex-col items-center justify-center z-10">
                            <span class="text-xl font-bold text-slate-800">{{ $totalLayanan ?? 0 }}</span>
                            <span class="text-[9px] text-slate-400">Total Layanan</span>
                        </div>
                    </div>

                    <div class="space-y-3 text-[10px] flex-1">
                        <div class="flex justify-between">
                            <span><i class="fa-solid fa-circle text-emerald-700 mr-1"></i> Memenuhi SLA</span>
                            <b>{{ $slaTerpenuhi ?? 0 }}</b>
                        </div>
                        <div class="flex justify-between">
                            <span><i class="fa-solid fa-circle text-amber-500 mr-1"></i> Mendekati SLA</span>
                            <b>{{ $slaMendekati ?? 0 }}</b>
                        </div>
                        <div class="flex justify-between">
                            <span><i class="fa-solid fa-circle text-rose-600 mr-1"></i> Melewati SLA</span>
                            <b>{{ $slaTerlewati ?? 0 }}</b>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TREND (DENGAN SUMBU Y & DATA DINAMIS) -->
            <div class="xl:col-span-8 bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <h3 class="font-bold text-xs text-slate-800">Tren Kepatuhan SLA</h3>
                <p class="text-[9px] text-slate-400 mt-1">Perbandingan kepatuhan SLA tahun {{ date('Y') }}</p>

                <div class="mt-4 flex items-center">
                    <!-- Label Angka Sumbu Y -->
                    <div class="w-8 h-52 flex flex-col justify-between text-[8px] text-slate-400 text-right pr-2 font-medium">
                        <span>100%</span>
                        <span>75%</span>
                        <span>50%</span>
                        <span>25%</span>
                        <span>0%</span>
                    </div>

                    <!-- Area Grafik Garis -->
                    <div class="flex-1 h-52 relative border-l border-b border-slate-200" style="background-image: linear-gradient(to bottom, #e2e8f0 1px, transparent 1px); background-size: 100% 25%;">
                        <div class="absolute inset-x-3 inset-y-3">
                            <svg viewBox="0 0 700 190" class="w-full h-full" preserveAspectRatio="none">
                                <!-- Garis Target 95% Dinamis dari Controller -->
                                <polyline
                                    points="{{ $targetPoints }}"
                                    fill="none"
                                    stroke="#f59e0b"
                                    stroke-width="2"
                                    stroke-dasharray="5 4"
                                />
                                <!-- Garis Kepatuhan Aktual Dinamis dari Database -->
                                <polyline
                                    points="{{ $chartPoints }}"
                                    fill="none"
                                    stroke="#052e23"
                                    stroke-width="3"
                                />
                            </svg>
                        </div>

                        <div class="absolute -bottom-5 left-0 right-0 flex justify-between text-[8px] text-slate-400 px-1">
                            <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span>
                            <span>May</span><span>Jun</span><span>Jul</span><span>Aug</span>
                            <span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-7 text-[9px] text-slate-500">
                    <span><i class="fa-solid fa-minus text-[#052e23] mr-1"></i>Kepatuhan SLA Aktual</span>
                    <span><i class="fa-solid fa-minus text-amber-500 mr-1"></i>Target Minimal (95%)</span>
                </div>
            </div>

            <!-- VIOLATION CATEGORY (DINAMIS) -->
            <div class="xl:col-span-4 bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <h3 class="font-bold text-xs text-slate-800">Distribusi Pelanggaran SLA per Kategori</h3>

                <div class="mt-5 space-y-4 text-[10px]">
                    @forelse($categoryPercentages ?? [] as $cat => $pct)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="capitalize">{{ str_replace('_', ' ', $cat) }}</span><b>{{ $pct }}%</b>
                            </div>
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-rose-500 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-center py-4">Belum ada data pelanggaran kategori.</p>
                    @endforelse

                    <div class="pt-3 border-t border-slate-100 text-slate-400">
                        Total Pelanggaran: <b class="text-slate-700">{{ $totalAllViolations ?? 0 }}</b>
                    </div>
                </div>
            </div>

            <!-- SUMMARY TABLE (DINAMIS) -->
            <div class="xl:col-span-7 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200">
                    <h3 class="font-bold text-xs text-slate-800">Ringkasan Layanan</h3>
                </div>

                <table class="w-full text-[10px]">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr class="border-b border-slate-200">
                            <th class="px-3 py-2 text-center">No</th>
                            <th class="px-3 py-2 text-left">Layanan</th>
                            <th class="px-3 py-2 text-center">Jumlah Pelanggaran</th>
                            <th class="px-3 py-2 text-center">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($services ?? [] as $index => $item)
                            <tr>
                                <td class="px-3 py-3 text-center">{{ $index + 1 }}</td>
                                <td class="px-3 py-3 font-semibold">{{ $item->name }}</td>
                                <td class="px-3 py-3 text-center">{{ $item->overdue_tickets ?? 0 }}</td>
                                <td class="px-3 py-3 text-center">
                                    {{ ($totalPelanggaran ?? 0) > 0 ? round((($item->overdue_tickets ?? 0) / $totalPelanggaran) * 100) : 0 }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">Tidak ada ringkasan data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- RECOMMENDATIONS -->
            <div class="xl:col-span-5 bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-xs text-slate-800">Aksi yang Disarankan</h3>
                    <i class="fa-solid fa-lightbulb text-amber-500"></i>
                </div>

                <div class="mt-4 space-y-3 text-[10px]">
                    <div class="flex gap-3 p-3 bg-rose-50 rounded-lg border border-rose-100">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 mt-0.5"></i>
                        <div>
                            <p class="font-bold text-slate-800">Evaluasi layanan dengan SLA terlewati</p>
                            <p class="text-slate-500 mt-0.5">Prioritaskan analisis penyebab dan rencana perbaikan.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 p-3 bg-amber-50 rounded-lg border border-amber-100">
                        <i class="fa-solid fa-clock text-amber-600 mt-0.5"></i>
                        <div>
                            <p class="font-bold text-slate-800">Pantau layanan yang mendekati SLA</p>
                            <p class="text-slate-500 mt-0.5">Lakukan pemantauan sebelum melewati target.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                        <i class="fa-solid fa-chart-line text-emerald-700 mt-0.5"></i>
                        <div>
                            <p class="font-bold text-slate-800">Pertahankan kepatuhan SLA</p>
                            <p class="text-slate-500 mt-0.5">Layanan dengan performa baik perlu dipertahankan.</p>
                        </div>
                    </div>

                    <button type="button"
                            class="w-full mt-1 py-2 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-semibold border border-slate-200 hover:bg-slate-200 transition">
                        <i class="fa-solid fa-list-check mr-1"></i>
                        Buat Catatan Evaluasi
                    </button>
                </div>
            </div>

        </section>

    </div>
</x-admin-layout>