<x-app-layout>
    <x-slot name="title">Tiket Saya - SLA Helpdesk</x-slot>
    <x-slot name="headerTitle">Tiket Saya</x-slot>

    <!-- WRAPPER STATE ALPINE FOR TICKETING PAGE -->
    <div x-data="{ 
        detailOpen: false,
        selectedTicket: null,
        statusFilter: 'all',
        priorityFilter: 'all'
    }">

        <!-- PAGE HEADER & ACTION BAR -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-xl">Kelola Tiket Laporan & Layanan</h3>
                    <p class="text-xs text-slate-500 mt-1">Daftar seluruh aduan dan permohonan bantuan teknis yang telah Anda ajukan.</p>
                </div>
                
                <a href="{{ route('create-ticket') }}" class="inline-flex items-center justify-center space-x-2 bg-[#022C22] hover:bg-emerald-900 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm self-start md:self-auto">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Buat Tiket Baru</span>
                </a>
            </div>

            <!-- FILTER BAR -->
            <div class="flex flex-col sm:flex-row items-center gap-3 mt-6 pt-5 border-t border-slate-100">
                <span class="text-xs font-bold text-slate-500 shrink-0 self-start sm:self-auto">Filter Data:</span>
                
                <!-- Filter Status -->
                <select x-model="statusFilter" class="w-full sm:w-auto px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#F59E0B] transition">
                    <option value="all">Semua Status</option>
                    <option value="submitted">Submitted</option>
                    <option value="verified">Verified</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                    <option value="rejected">Rejected</option>
                </select>

                <!-- Filter Prioritas -->
                <select x-model="priorityFilter" class="w-full sm:w-auto px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#F59E0B] transition">
                    <option value="all">Semua Prioritas</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
        </div>

        <!-- NOTIFICATION ALERT -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- TICKET TABLE CARD -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-700 bg-slate-50/50">
                            <th class="py-3.5 px-3">No Ticket</th>
                            <th class="py-3.5 px-3">Layanan</th>
                            <th class="py-3.5 px-3">Judul Kendala</th>
                            <th class="py-3.5 px-3">Kategori</th>
                            <th class="py-3.5 px-3">Prioritas</th>
                            <th class="py-3.5 px-3">Status</th>
                            <th class="py-3.5 px-3">SLA Resolution</th>
                            <th class="py-3.5 px-3">Tanggal Dibuat</th>
                            <th class="py-3.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-600">
                        
                        @forelse($tickets as $ticket)
                            <tr x-show="(statusFilter === 'all' || statusFilter === '{{ $ticket->status }}') &&
                                    (priorityFilter === 'all' || priorityFilter === '{{ $ticket->priority }}')"
                                class="hover:bg-slate-50/80 transition">
                                
                                <td class="py-4 px-3 font-bold text-emerald-700 whitespace-nowrap">{{ $ticket->ticket_no }}</td>
                                <td class="py-4 px-3 text-slate-700 font-semibold whitespace-nowrap">{{ $ticket->service->name ?? 'Umum' }}</td>
                                <td class="py-4 px-3 min-w-[220px]">
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
                                
                                <!-- KOLOM SLA RESOLUTION DINAMIS -->
                                <td class="py-4 px-3 whitespace-nowrap">
                                    @php
                                        $rawJangkaWaktu = $ticket->service->jangka_waktu ?? '2 Hari Kerja';
                                        
                                        preg_match('/\d+/', $rawJangkaWaktu, $matches);
                                        $jumlahAngka = isset($matches[0]) ? (int)$matches[0] : 2;
                                        
                                        $isHari = stripos($rawJangkaWaktu, 'hari') !== false;
                                        $limitJam = $isHari ? ($jumlahAngka * 24) : $jumlahAngka;
                                        
                                        $diffJam = $ticket->created_at->diffInHours(now());
                                        $isBreached = ($diffJam > $limitJam) && !in_array($ticket->status, ['resolved', 'closed', 'completed']);
                                    @endphp

                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-slate-700">
                                            {{ $rawJangkaWaktu }}
                                        </span>

                                        @if(in_array($ticket->status, ['resolved', 'closed', 'completed']))
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                                                <i class="fa-solid fa-circle-check"></i> Selesai
                                            </span>
                                        @elseif($isBreached)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Terlewat ({{ $diffJam - $limitJam }} Jam)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                <i class="fa-solid fa-clock"></i> On Track
                                            </span>
                                        @endif
                                    </div>
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
                                        
                                        /* TARGET SLA RESOLUSI DINAMIS */
                                        'resolution_target' => $rawJangkaWaktu, 
                                        
                                        'is_breached' => $isBreached,
                                        'diff_hours' => $diffJam - $limitJam,
                                        'technician' => $ticket->technician ?? 'Belum Ditugaskan',
                                        'technician_notes' => $ticket->technician_notes ?? 'Belum ada catatan penanganan dari teknisi.',
                                        
                                        /* LAMPIRAN BUKTI DARI TEKNISI */
                                        'attachment' => $ticket->technician_file ?? null
                                    ]) }}; detailOpen = true;" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-[11px]">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400 font-medium">
                                    <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                                    Belum ada tiket aduan yang diajukan.
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
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-slate-700">Resolution Time</span>
                            <template x-if="selectedTicket?.is_breached && !['resolved', 'closed'].includes(selectedTicket?.status)">
                                <span class="font-bold text-rose-600 text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Terlewat <span x-text="selectedTicket?.diff_hours"></span> Jam
                                </span>
                            </template>
                            <template x-if="!selectedTicket?.is_breached || ['resolved', 'closed'].includes(selectedTicket?.status)">
                                <span class="font-bold text-emerald-600 text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-check text-[10px]"></i> On Track / Selesai
                                </span>
                            </template>
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
</x-app-layout>