<x-teknisi-layout>
    <x-slot name="title">Ticketing Teknisi - Sistem SLA</x-slot>
    <x-slot name="headerTitle">Ticketing Teknisi</x-slot>

    <!-- CONTENT CONTAINER -->
    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Breadcrumb Navigation -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('teknisi.dashboard') }}" class="hover:text-emerald-700">Dashboard</a>
            <span>&gt;</span>
            <span class="font-semibold text-slate-800">Ticketing</span>
        </div>

        <!-- Summary Statistic Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-slate-100 rounded-lg text-slate-600"><i class="fa-regular fa-file-lines text-lg"></i></div>
                <div><p class="text-xs font-semibold text-slate-500">Total Ticket</p><h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $totalTicket ?? 0 }}</h3></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-blue-50 rounded-lg text-blue-600"><i class="fa-solid fa-spinner text-lg"></i></div>
                <div><p class="text-xs font-semibold text-slate-500">Dalam Proses</p><h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $dalamProses ?? 0 }}</h3></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-amber-50 rounded-lg text-amber-600"><i class="fa-regular fa-clock text-lg"></i></div>
                <div><p class="text-xs font-semibold text-slate-500">Mendekati SLA</p><h3 class="text-xl font-bold text-slate-800 mt-0.5" id="statMendekatiSla">{{ $mendekatiSla ?? 0 }}</h3></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-rose-50 rounded-lg text-rose-600"><i class="fa-solid fa-triangle-exclamation text-lg"></i></div>
                <div><p class="text-xs font-semibold text-slate-500">Melewati SLA</p><h3 class="text-xl font-bold text-slate-800 mt-0.5" id="statMelewatiSla">{{ $melewatiSla ?? 0 }}</h3></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                <div class="p-2.5 bg-emerald-50 rounded-lg text-emerald-600"><i class="fa-regular fa-circle-check text-lg"></i></div>
                <div><p class="text-xs font-semibold text-slate-500">Selesai</p><h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $selesai ?? 0 }}</h3></div>
            </div>
        </div>

        <!-- Main Ticketing Container -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex border-b border-slate-200 px-6 pt-4 gap-8 text-sm font-medium text-slate-500">
                <button class="pb-3 border-b-2 border-[#052e23] font-bold text-[#052e23] flex items-center gap-2">
                    Semua Ticket <span class="text-xs bg-emerald-100 text-[#052e23] px-2 py-0.5 rounded-full font-bold">{{ isset($tickets) ? $tickets->count() : 0 }}</span>
                </button>
            </div>

            <!-- FORM FILTER & SEARCH -->
            <form method="GET" action="{{ route('teknisi.ticketing') }}" class="p-4 bg-slate-50/50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="relative min-w-60 flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tiket, judul, atau pelapor..." class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-700 text-slate-700">
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-600">
                        <option value="">Semua Status</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>submitted</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>in_progress</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>closed</option>
                    </select>
                    @if(request('search') || request('status'))
                        <a href="{{ route('teknisi.ticketing') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-semibold transition" title="Reset Filter"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>

            <!-- Tickets Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-xs font-semibold border-b border-slate-200">
                            <th class="py-3.5 px-3">Id Ticket</th>
                            <th class="py-3.5 px-3">Layanan</th>
                            <th class="py-3.5 px-3">Perangkat Daerah</th>
                            <th class="py-3.5 px-3">Judul Kendala</th>
                            <th class="py-3.5 px-3">Pelapor</th>
                            <th class="py-3.5 px-3">Kategori</th>
                            <th class="py-3.5 px-3">Prioritas</th>
                            <th class="py-3.5 px-3">Status</th>
                            <th class="py-3.5 px-3">SLA</th>
                            <th class="py-3.5 px-3">Sisa Waktu</th>
                            <th class="py-3.5 px-3">Dibuat</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @if(isset($tickets) && count($tickets) > 0)
                            @foreach ($tickets as $ticket)
                                @php
                                    $status = strtoupper($ticket->status ?? '');
                                    $statusColor = 'bg-slate-100 text-slate-700';
                                    if (in_array($status, ['RESOLVED', 'CLOSED', 'COMPLETED'])) {
                                        $statusColor = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                                    } elseif (in_array($status, ['IN_PROGRESS', 'PROCESS'])) {
                                        $statusColor = 'bg-amber-50 text-amber-700 border border-amber-200';
                                    } elseif (in_array($status, ['SUBMITTED', 'PENDING'])) {
                                        $statusColor = 'bg-blue-50 text-blue-700 border border-blue-200';
                                    }

                                    $priority = strtoupper($ticket->priority ?? 'MEDIUM');
                                    $priorityColor = 'bg-slate-100 text-slate-700';
                                    if ($priority === 'URGENT') {
                                        $priorityColor = 'bg-rose-50 text-rose-700 border border-rose-200';
                                    } elseif ($priority === 'HIGH') {
                                        $priorityColor = 'bg-amber-50 text-amber-700 border border-amber-200';
                                    } elseif ($priority === 'MEDIUM') {
                                        $priorityColor = 'bg-blue-50 text-blue-700 border border-blue-200';
                                    } elseif ($priority === 'LOW') {
                                        $priorityColor = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                                    }

                                    $isDone = in_array(strtolower($ticket->status), ['resolved', 'completed', 'closed']);
                                    
                                    // Logika Jangka Waktu (SLA) yang diperbaiki
                                    $rawJangkaWaktu = $ticket->service->jangka_waktu ?? '8 Jam';
                                    preg_match('/\d+/', $rawJangkaWaktu, $matches); 
                                    $jumlahAngka = isset($matches[0]) ? (int)$matches[0] : 8;
                                    $isHari = (stripos($rawJangkaWaktu, 'hari') !== false);
                                    $limitJam = $isHari ? ($jumlahAngka * 24) : $jumlahAngka;
                                    $durationMs = $limitJam * 3600 * 1000;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3 font-semibold text-emerald-800">{{ $ticket->ticket_no ?? 'SLA-'.$ticket->id }}</td>
                                    <td class="py-3.5 px-3 font-medium">{{ $ticket->service->name ?? '-' }}</td>
                                    <td class="py-3.5 px-3 min-w-40">{{ $ticket->service->department_name ?? $ticket->user->department_name ?? '-' }}</td>
                                    <td class="py-3.5 px-3 max-w-xs truncate font-bold text-slate-900">{{ $ticket->title ?? '-' }}</td>
                                    <td class="py-3.5 px-3">{{ $ticket->user->name ?? 'Pelapor' }}</td>
                                    <td class="py-3.5 px-3"><span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded uppercase text-[10px]">{{ $ticket->category ?? 'Umum' }}</span></td>
                                    <td class="py-3.5 px-3"><span class="px-2 py-0.5 rounded font-semibold uppercase text-[10px] {{ $priorityColor }}">{{ $ticket->priority ?? 'medium' }}</span></td>
                                    <td class="py-3.5 px-3"><span class="px-2.5 py-1 rounded-full font-bold uppercase text-[10px] {{ $statusColor }}">{{ strtolower($ticket->status) }}</span></td>
                                    <td class="py-3.5 px-3 font-semibold text-slate-700">{{ $rawJangkaWaktu }}</td>
                                    
                                    <!-- Kolom Sisa Waktu dengan Atribut Target & Kelas countdown-parent -->
                                    <td class="py-3.5 px-3 font-semibold countdown-parent" @if(!$isDone) data-created="{{ $ticket->created_at->toIso8601String() }}" data-duration-ms="{{ $durationMs }}" @endif>
                                        @if($isDone)
                                            <span class="text-emerald-600 font-semibold flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Selesai</span>
                                        @else
                                            <span class="countdown-timer text-amber-600 font-bold">Memuat...</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-3 text-slate-500 whitespace-nowrap">{{ $ticket->created_at ? $ticket->created_at->format('d M Y, H:i') : '-' }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <button data-ticket="{{ json_encode([
                                            'id' => $ticket->id,
                                            'ticket_no' => $ticket->ticket_no,
                                            'title' => $ticket->title,
                                            'description' => $ticket->description,
                                            'service' => ['name' => $ticket->service->name ?? 'Layanan Umum'],
                                            'priority' => $ticket->priority,
                                            'status' => $ticket->status,
                                            'technician' => $ticket->technician,
                                            'attachments' => $ticket->attachments,
                                            'image' => $ticket->image,
                                            'attachment' => $ticket->attachment,
                                            'file' => $ticket->file
                                        ]) }}" data-pelapor="{{ $ticket->user->name ?? 'Pelapor' }}" onclick="handleKelolaBtn(this)" class="px-3 py-1.5 bg-[#052e23] hover:bg-emerald-900 text-white rounded-lg font-semibold text-[11px] shadow-sm transition">
                                            Kelola
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="12" class="text-center py-6 text-slate-400">Belum ada tiket yang terdaftar.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL POP UP TINDAK LANJUT TIKET -->
    <div id="modalTindakLanjut" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden transition-all max-h-[90vh] overflow-y-auto">
            <div class="bg-[#052e23] px-6 py-4 flex justify-between items-center text-white sticky top-0 z-10">
                <div>
                    <h3 id="modalTicketNo" class="font-bold text-lg">SLA-00000</h3>
                    <p class="text-xs text-emerald-200">Tindak Lanjut Tiket</p>
                </div>
                <button onclick="closeModal()" class="text-white hover:text-slate-200 text-xl font-bold">&times;</button>
            </div>

            <form id="formTindakLanjut" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                
                <div class="border rounded-lg p-4 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h4 id="modalLayanan" class="font-bold text-slate-800">Layanan Digital</h4>
                        <p class="text-xs text-slate-500">Dilaporkan Oleh: <span id="modalPelapor" class="font-semibold text-slate-700">-</span></p>
                    </div>
                    <span id="modalPrioritas" class="bg-amber-100 text-amber-800 text-xs px-3 py-1 rounded-lg font-bold">MEDIUM</span>
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Deskripsi Kendala</label>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 min-h-15 whitespace-pre-line" id="modalDeskripsi">-</div>
                </div>

                <div id="modalImageContainer" class="space-y-1">
                    <label class="block font-bold text-slate-700">Lampiran Gambar Bukti</label>
                    <div class="w-full h-40 bg-slate-100 border border-slate-200 rounded-lg overflow-hidden flex items-center justify-center">
                        <img id="modalTicketImage" src="" alt="Bukti Kendala" class="w-full h-full object-contain cursor-pointer" onclick="window.open(this.src, '_blank')">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Teknisi</label>
                    <input type="text" name="technician" id="modalTechnician" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-slate-700" placeholder="Masukkan nama teknisi..." required>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Tiket</label>
                    <select name="status" id="modalStatus" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 font-semibold text-slate-700">
                        <option value="submitted">Submitted</option>
                        <option value="verified">Verified</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

<div>
    <label class="block font-bold text-slate-700 mb-1">Catatan Penanganan</label>
    <textarea name="technician_notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-slate-700" placeholder="Tuliskan catatan penanganan..."></textarea>
</div>

<div>
    <label class="block font-bold text-slate-700 mb-1">Lampiran / Bukti Penanganan <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span></label>
    <input type="file" name="technician_file" class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-white text-slate-700 text-xs file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100 cursor-pointer">
</div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#022C22] text-white rounded-lg font-bold hover:bg-emerald-900 transition shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT MODAL & TIMER -->
    <script>
        function handleKelolaBtn(button) {
            try {
                const ticket = JSON.parse(button.getAttribute('data-ticket'));
                const pelapor = button.getAttribute('data-pelapor');

                document.getElementById('modalTicketNo').innerText = ticket.ticket_no || 'SLA-' + ticket.id;
                document.getElementById('modalLayanan').innerText = ticket.service && ticket.service.name ? ticket.service.name : 'Layanan Digital';
                document.getElementById('modalPelapor').innerText = pelapor || 'Pelapor';
                document.getElementById('modalPrioritas').innerText = (ticket.priority || 'MEDIUM').toUpperCase();
                
                document.getElementById('modalDeskripsi').innerText = ticket.description || ticket.body || ticket.content || 'Tidak ada deskripsi.';
                document.getElementById('modalTechnician').value = ticket.technician || '{{ auth()->user()->name }}';
                document.getElementById('modalStatus').value = (ticket.status || 'in_progress').toLowerCase();
                
                // Route action form diarahkan khusus ke rute teknisi
                document.getElementById('formTindakLanjut').action = `/teknisi/tickets/${ticket.id}/update-status`;

                const ticketImage = document.getElementById('modalTicketImage');
                const imageContainer = document.getElementById('modalImageContainer');
                let imagePath = ticket.attachments?.[0]?.file_path || ticket.image || ticket.attachment || ticket.file;
                
                if (imagePath) {
                    ticketImage.src = imagePath.startsWith('http') || imagePath.startsWith('/') ? imagePath : `/storage/${imagePath}`;
                    imageContainer.style.display = 'block';
                } else {
                    imageContainer.style.display = 'none';
                }

                const modal = document.getElementById('modalTindakLanjut');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } catch (error) {
                console.error('Gagal membuka modal:', error);
            }
        }

        function closeModal() {
            const modal = document.getElementById('modalTindakLanjut');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        // Live Countdown Script yang diperbarui
        document.addEventListener("DOMContentLoaded", function () {
            const parentCells = document.querySelectorAll('.countdown-parent');

            parentCells.forEach(cell => {
                const timerSpan = cell.querySelector('.countdown-timer');
                if (!timerSpan) return;

                const createdStr = cell.getAttribute('data-created');
                const durationMs = parseInt(cell.getAttribute('data-duration-ms')) || (8 * 3600 * 1000);

                if (!createdStr) return;

                const createdAt = new Date(createdStr).getTime();
                const deadline = createdAt + durationMs;

                function updateCountdown() {
                    const now = new Date().getTime();
                    const distance = deadline - now;

                    if (distance < 0) {
                        timerSpan.innerHTML = '<span class="text-rose-600 font-bold">Lewat SLA</span>';
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    let timeString = '';
                    if (days > 0) {
                        timeString += `${days}h `;
                    }
                    timeString += `${hours}j ${minutes}m ${seconds}d`;
                    
                    timerSpan.innerText = timeString;
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            });
        });
    </script>
</x-teknisi-layout>