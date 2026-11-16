<header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 shrink-0">
    <!-- Left Section: Toggle & Title -->
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-all focus:outline-none">
            <i class="fa-solid fa-bars text-sm"></i>
        </button>
        <div>
            <h2 class="font-bold text-slate-800 text-lg leading-tight">{{ $headerTitle ?? 'Dashboard' }}</h2>
            <p class="text-xs text-slate-500">Sistem SLA & Support Pemkab Bandung Barat</p>
        </div>
    </div>

    <!-- Center Section: Search Input -->
    <div class="relative hidden sm:block">
        <input type="text" 
               x-model="searchQuery" 
               placeholder="Cari tiket atau layanan..." 
               class="w-64 pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-[#F59E0B] transition-all">
        <span class="absolute left-3 top-2.5 text-slate-400 text-sm">🔍</span>
    </div>

    <!-- Right Section: Notification & Profile -->
    <div class="flex items-center gap-3 sm:gap-4">
        
        <!-- TOMBOL NOTIFIKASI & DROPDOWN -->
        <div class="relative" x-data="{ notifOpen: false }">
            <!-- Bell Button -->
            <button 
                @click="notifOpen = !notifOpen" 
                @click.outside="notifOpen = false" 
                class="relative p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all focus:outline-none">
                <i class="fa-regular fa-bell text-base"></i>
                
                <!-- Red Badge Indicator (Sinkron Query Langsung ke Database) -->
                @php
                    $userId = Auth::id();
                    $unreadCount = \App\Models\Ticket::where('user_id', $userId)
                                                    ->whereIn('status', ['submitted', 'in_progress'])
                                                    ->count();
                    $notifTickets = \App\Models\Ticket::where('user_id', $userId)
                                                      ->latest()
                                                      ->take(5)
                                                      ->get();
                @endphp
                
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-rose-500 text-[9px] font-extrabold text-white items-center justify-center">
                            {{ $unreadCount }}
                        </span>
                    </span>
                @endif
            </button>

            <!-- Dropdown Content -->
            <div 
                x-show="notifOpen" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden"
                style="display: none;">
                
                <!-- Header Dropdown -->
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <h4 class="font-extrabold text-slate-800 text-sm">Notifikasi SLA</h4>
                        @if($unreadCount > 0)
                            <span class="px-2 py-0.5 bg-rose-50 text-rose-600 text-[10px] font-bold rounded-full border border-rose-100">
                                {{ $unreadCount }} Aktif
                            </span>
                        @endif
                    </div>
                    <button class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">Tandai Dibaca</button>
                </div>

                <!-- List Notifikasi Dinamis -->
                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                    @forelse($notifTickets as $notifTicket)
                        <a href="#" class="flex gap-3 p-3.5 hover:bg-slate-50 transition {{ $notifTicket->status === 'in_progress' ? 'bg-amber-50/30' : '' }}">
                            
                            @if($notifTicket->status === 'in_progress')
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                </div>
                            @elseif($notifTicket->status === 'resolved')
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-paper-plane text-xs"></i>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-800 leading-snug">
                                    Tiket {{ $notifTicket->ticket_no ?? 'SLA-'.$notifTicket->id }}
                                </p>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    Status: <span class="font-semibold text-slate-700 uppercase">{{ str_replace('_', ' ', $notifTicket->status) }}</span> — {{ $notifTicket->title }}
                                </p>
                                <span class="text-[10px] font-medium text-slate-400 mt-1 block">
                                    {{ $notifTicket->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fa-regular fa-bell-slash text-2xl mb-1.5 block"></i>
                            Belum ada pemberitahuan notifikasi.
                        </div>
                    @endforelse
                </div>

                <!-- Footer Dropdown (Menggunakan route user.ticketing yang valid) -->
                <div class="p-3 border-t border-slate-100 text-center bg-slate-50/50">
                    <a href="{{ route('user.ticketing') }}" class="text-xs font-bold text-slate-700 hover:text-slate-900 transition block">
                        Lihat Semua Tiket Aduan
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Info -->
        <div class="flex items-center gap-3 border-l border-slate-200 pl-4">
            <div class="w-9 h-9 rounded-full bg-[#0F172A] text-white flex items-center justify-center font-semibold text-xs shadow-sm">
                {{ strtoupper(substr(Auth::user()->name ?? 'User', 0, 2)) }}
            </div>
            <div class="hidden md:block">
                <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-[10px] text-slate-500 uppercase">{{ Auth::user()->role ?? 'Pengguna' }}</p>
            </div>
        </div>

    </div>
</header>