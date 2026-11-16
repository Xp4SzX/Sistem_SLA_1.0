<x-app-layout>
    <x-slot name="title">Dashboard - SLA Helpdesk</x-slot>
    <x-slot name="headerTitle">Dashboard Pengguna</x-slot>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/pelapor/dashboard.js'])

    <!-- LOAD CHART.JS ENGINE -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- WRAPPER STATE ALPINE FOR DETAIL DRAWER -->
    <div x-data="{ detailOpen: false, selectedTicket: null }">

        <!-- METRIC CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs text-slate-500 font-medium">Total Tiket Saya</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total'] ?? 0 }}</h3>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs text-blue-500 font-medium">Sedang Diproses</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] ?? 0 }}</h3>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs text-emerald-500 font-medium">Selesai Ditangani</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['resolved'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- CHARTS SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Chart Status Tiket (Doughnut) -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h4 class="font-extrabold text-slate-800 text-sm mb-1">Persentase Status Laporan</h4>
                <p class="text-xs text-slate-500 mb-4">Ringkasan tiket berdasarkan progress penanganan</p>
                <div class="h-56 relative flex items-center justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Chart Kategori Laporan (Bar) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h4 class="font-extrabold text-slate-800 text-sm mb-1">Statistik Kategori Kendala</h4>
                <p class="text-xs text-slate-500 mb-4">Jumlah laporan aduan berdasarkan jenis kategori</p>
                <div class="h-56 relative">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- TABLE CONTAINER CARD -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            
            <!-- Table Header & Action Button -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-lg">Daftar Tiket Aduan & Pelayanan</h3>
                    <p class="text-xs text-slate-500 font-medium">Pantau status penanganan dan batas waktu SLA laporan Anda</p>
                </div>
                <a href="{{ route('create-ticket') }}" class="inline-flex items-center justify-center space-x-2 bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-sm self-start sm:self-auto">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Buat Tiket Baru</span>
                </a>
            </div>

            <!-- Notification Alert -->
            @if(session('success'))
                <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Table Responsive -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-700 bg-slate-50/50">
                            <th class="py-3 px-3">No Ticket</th>
                            <th class="py-3 px-3">Layanan</th>
                            <th class="py-3 px-3">Judul Kendala</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3">Prioritas</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Tanggal Dibuat</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-600">
                        
                        @forelse($tickets as $ticket)
                            <tr x-show="searchQuery === '' || 
                                        '{{ addslashes(strtolower($ticket->ticket_no)) }}'.includes(searchQuery.toLowerCase()) || 
                                        '{{ addslashes(strtolower($ticket->service->name ?? '')) }}'.includes(searchQuery.toLowerCase()) || 
                                        '{{ addslashes(strtolower($ticket->title)) }}'.includes(searchQuery.toLowerCase()) ||
                                        '{{ addslashes(strtolower($ticket->category)) }}'.includes(searchQuery.toLowerCase())"
                                class="hover:bg-slate-50/80 transition">
                                
                                <td class="py-4 px-3 font-bold text-emerald-700 whitespace-nowrap">{{ $ticket->ticket_no }}</td>
                                <td class="py-4 px-3 text-slate-700 font-semibold whitespace-nowrap">{{ $ticket->service->name ?? 'Umum' }}</td>
                                <td class="py-4 px-3 min-w-[200px]">
                                    <p class="font-bold text-slate-800 line-clamp-1">{{ $ticket->title }}</p>
                                </td>
                                <td class="py-4 px-3 whitespace-nowrap">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-bold rounded-md text-[10px] uppercase">{{ $ticket->category }}</span>
                                </td>
                                <td class="py-4 px-3 whitespace-nowrap">
                                    @php
                                        $priorityClasses = [
                                            'low' => 'bg-slate-100 text-slate-600 border-slate-200',
                                            'medium' => 'bg-blue-50 text-blue-600 border-blue-200',
                                            'high' => 'bg-amber-50 text-amber-600 border-amber-200',
                                            'urgent' => 'bg-rose-50 text-rose-600 border-rose-200',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 font-bold rounded-lg text-[10px] border uppercase {{ $priorityClasses[$ticket->priority] ?? 'bg-slate-100' }}">
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
                                <td class="py-4 px-3 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'submitted' => 'bg-blue-100/60 text-blue-700',
                                            'verified' => 'bg-purple-100/60 text-purple-700',
                                            'in_progress' => 'bg-amber-100/60 text-amber-700',
                                            'resolved' => 'bg-emerald-100/60 text-emerald-700',
                                            'closed' => 'bg-slate-200 text-slate-700',
                                            'rejected' => 'bg-rose-100/60 text-rose-700',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 font-bold rounded-lg text-[10px] uppercase {{ $statusClasses[$ticket->status] ?? 'bg-slate-100' }}">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-3 text-slate-500 whitespace-nowrap">{{ $ticket->created_at->format('d M Y, H:i') }}</td>
                                <td class="py-4 px-3 text-center whitespace-nowrap">
                                    <button @click="selectedTicket = {{ json_encode([
                                        'ticket_no' => $ticket->ticket_no,
                                        'title' => $ticket->title,
                                        'description' => $ticket->description,
                                        'service_name' => $ticket->service->name ?? 'Layanan Umum',
                                        'category' => $ticket->category,
                                        'priority' => $ticket->priority,
                                        'status' => $ticket->status,
                                        'created_at' => $ticket->created_at->format('d M Y, H:i'),
                                        'response_time' => $ticket->first_responded_at ? $ticket->created_at->diffInMinutes($ticket->first_responded_at) . ' menit' : 'Belum direspon',
                                        'response_target' => '30 menit',
                                        
                                        /* TARGET SLA RESOLUSI DINAMIS BERDASARKAN LAYANAN */
                                        'resolution_target' => $ticket->service->jangka_waktu ?? '8 Jam',
                                        
                                        'technician' => $ticket->technician ?? 'Belum Ditugaskan',
                                        'technician_notes' => $ticket->technician_notes ?? 'Belum ada catatan penanganan dari teknisi.',
                                        
                                        /* BERKAS BUKTI / LAMPIRAN DARI TEKNISI */
                                        'attachment' => $ticket->technician_file ?? null
                                    ]) }}; detailOpen = true;" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-[11px]">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 font-medium">
                                    <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                                    Belum ada tiket laporan yang diajukan.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>

        <!-- DETAIL TIKET DRAWER / SIDE-SHEET MODAL -->
        <div x-show="detailOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="detailOpen = false" 
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"
             style="display: none;">
        </div>

        <div x-show="detailOpen" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-2xl z-50 flex flex-col overflow-hidden"
             style="display: none;">

            <!-- Header Gelap -->
            <div class="bg-[#0B132B] text-white p-5 flex items-center justify-between shrink-0">
                <div>
                    <span class="text-xs font-bold text-[#F59E0B] tracking-wider uppercase" x-text="selectedTicket?.ticket_no || 'SLA-000'"></span>
                    <h3 class="text-lg font-bold text-white mt-0.5">Detail Tiket</h3>
                </div>
                <button @click="detailOpen = false" class="w-8 h-8 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition focus:outline-none">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Body Scrollable -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                <!-- Title & Tags -->
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 leading-snug" x-text="selectedTicket?.title"></h2>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed" x-text="selectedTicket?.description"></p>
                    
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="px-3 py-1 bg-amber-50 text-amber-600 font-bold rounded-lg text-xs border border-amber-200/50 uppercase" x-text="selectedTicket?.priority"></span>
                        <span class="px-3 py-1 bg-amber-100/60 text-amber-700 font-bold rounded-lg text-xs uppercase" x-text="selectedTicket?.status"></span>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 font-medium rounded-lg text-xs" x-text="selectedTicket?.service_name"></span>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 font-medium rounded-lg text-xs uppercase" x-text="selectedTicket?.category"></span>
                    </div>
                </div>

                <!-- Info Box Dibuat & Teknisi -->
                <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-400 font-medium">Dibuat: </span>
                        <span class="font-bold text-slate-800" x-text="selectedTicket?.created_at"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Teknisi: </span>
                        <span class="font-bold text-slate-800" x-text="selectedTicket?.technician"></span>
                    </div>
                </div>

                <!-- Progress Penanganan Vertical Timeline -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">PROGRESS PENANGANAN</h4>

                    <div class="relative pl-3 space-y-6">
                        
                        <!-- Step 1: Submitted -->
                        <div class="relative flex items-start group">
                            <div class="absolute -left-[11px] top-0 bottom-0 w-0.5" :class="['verified', 'assigned', 'in_progress', 'resolved', 'closed'].includes(selectedTicket?.status) ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                            <div class="absolute -left-5 top-0 w-6 h-6 rounded-full text-white flex items-center justify-center text-xs shadow-sm z-10" :class="selectedTicket?.status ? 'bg-emerald-500' : 'bg-slate-200'">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            <div class="pl-5">
                                <h5 class="text-xs font-bold text-slate-800">Submitted</h5>
                                <p class="text-[10px] font-medium text-slate-400 mt-0.5">Tiket berhasil dikirim ke sistem</p>
                            </div>
                        </div>

                        <!-- Step 2: Verified -->
                        <div class="relative flex items-start group">
                            <div class="absolute -left-[11px] top-0 bottom-0 w-0.5" :class="['assigned', 'in_progress', 'resolved', 'closed'].includes(selectedTicket?.status) ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                            <div class="absolute -left-5 top-0 w-6 h-6 rounded-full text-white flex items-center justify-center text-xs shadow-sm z-10" :class="['verified', 'assigned', 'in_progress', 'resolved', 'closed'].includes(selectedTicket?.status) ? 'bg-emerald-500' : 'bg-slate-200 text-slate-400'">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            <div class="pl-5">
                                <h5 class="text-xs font-bold" :class="['verified', 'assigned', 'in_progress', 'resolved', 'closed'].includes(selectedTicket?.status) ? 'text-slate-800' : 'text-slate-400'">Verified</h5>
                                <p class="text-[10px] font-medium text-slate-400 mt-0.5">Diverifikasi oleh Desk Helpdesk</p>
                            </div>
                        </div>

                        <!-- Step 3: In Progress -->
                        <div class="relative flex items-start group">
                            <div class="absolute -left-[11px] top-0 bottom-0 w-0.5" :class="['resolved', 'closed'].includes(selectedTicket?.status) ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                            <div class="absolute -left-5 top-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold z-10" :class="selectedTicket?.status === 'in_progress' ? 'bg-amber-500 text-white' : (['resolved', 'closed'].includes(selectedTicket?.status) ? 'bg-emerald-500 text-white' : 'bg-slate-100 border border-slate-200 text-slate-400')">
                                3
                            </div>
                            <div class="pl-5">
                                <h5 class="text-xs font-bold" :class="selectedTicket?.status === 'in_progress' ? 'text-amber-600' : (['resolved', 'closed'].includes(selectedTicket?.status) ? 'text-slate-800' : 'text-slate-400')">In Progress</h5>
                                <p class="text-[11px] text-slate-500 mt-0.5">Sedang ditangani oleh teknisi</p>
                            </div>
                        </div>

                        <!-- Step 4: Resolved -->
                        <div class="relative flex items-start group">
                            <div class="absolute -left-5 top-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold z-10" :class="['resolved', 'closed'].includes(selectedTicket?.status) ? 'bg-emerald-500 text-white' : 'bg-slate-100 border border-slate-200 text-slate-400'">
                                4
                            </div>
                            <div class="pl-5">
                                <h5 class="text-xs font-bold" :class="['resolved', 'closed'].includes(selectedTicket?.status) ? 'text-emerald-600' : 'text-slate-400'">Resolved</h5>
                                <p class="text-[10px] font-medium text-slate-400 mt-0.5">Kendala telah diselesaikan</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Card SLA Tracker -->
                <div class="bg-slate-50/50 border border-slate-200/60 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 tracking-wider uppercase">SLA TRACKER</span>
                    </div>

                    <!-- Response Time -->
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-slate-700">Response Time</span>
                            <span class="font-bold text-emerald-600 text-[11px] flex items-center gap-1">
                                <i class="fa-solid fa-check text-[10px]"></i> On Track
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-slate-200/60 text-slate-600 font-medium rounded-lg text-xs">Target: <strong class="text-slate-800" x-text="selectedTicket?.response_target"></strong></span>
                            <span class="px-2.5 py-1 bg-emerald-100/60 text-emerald-700 font-medium rounded-lg text-xs">Aktual: <strong class="text-emerald-800" x-text="selectedTicket?.response_time"></strong></span>
                        </div>
                    </div>

                    <!-- Resolution Time -->
                    <div class="pt-2 border-t border-slate-200/60">
                        <div class="text-xs mb-1.5">
                            <span class="font-bold text-slate-700">Resolution Time</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-slate-200/60 text-slate-600 font-medium rounded-lg text-xs">Target SLA: <strong class="text-slate-800" x-text="selectedTicket?.resolution_target"></strong></span>
                        </div>
                    </div>
                </div>

                <!-- Box Catatan Teknisi -->
                <div class="bg-amber-50/50 border border-amber-200/80 rounded-2xl p-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-amber-800 mb-2">
                        <span>📝</span> Catatan Teknisi
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed" x-text="selectedTicket?.technician_notes"></p>
                </div>

                <!-- Box Lampiran / Berkas Bukti Penanganan Teknisi -->
                <template x-if="selectedTicket?.attachment">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-2">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lampiran / Berkas Penanganan Teknisi</h4>
                        <a :href="selectedTicket.attachment.startsWith('http') ? selectedTicket.attachment : '/storage/' + selectedTicket.attachment" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-emerald-800 hover:bg-slate-50 transition shadow-sm">
                            <i class="fa-solid fa-paperclip"></i> Lihat Berkas / Gambar Bukti Teknisi
                        </a>
                    </div>
                </template>

            </div>

        </div>

    </div>

    <!-- OPER DATA PHP KE GLOBAL WINDOW UNTUK SCRIPT EKSTERNAL -->
    @php
        $categoryCounts = $tickets->groupBy('category')->map->count();
    @endphp
    <script>
        window.pelaporStats = {
            in_progress: {{ $stats['in_progress'] ?? 0 }},
            resolved: {{ $stats['resolved'] ?? 0 }},
            need_rating: {{ $stats['need_rating'] ?? 0 }}
        };
        window.pelaporCategoryData = {
            labels: {!! json_encode($categoryCounts->keys()->map(fn($cat) => strtoupper($cat))) !!},
            values: {!! json_encode($categoryCounts->values()) !!}
        };
    </script>
</x-app-layout>