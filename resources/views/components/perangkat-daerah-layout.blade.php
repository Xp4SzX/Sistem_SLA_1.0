<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Perangkat Daerah - Sistem SLA' }}</title>
    
    <!-- FAVICON LOGO KBB -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kbb.png') }}">
    
    <!-- Tailwind CSS & JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kbb.png') }}">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{ $styles ?? '' }}
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-800 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true, profileDropdown: false }">

    <!-- SIDEBAR PERANGKAT DAERAH (ADMIN DINAS) -->
    <aside class="bg-[#052e23] text-white flex flex-col justify-between shrink-0 transition-all duration-300"
           :class="sidebarOpen ? 'w-64' : 'w-20'">
        <div>
            <!-- Brand Header -->
            <div class="p-5 flex items-center gap-3 border-b border-emerald-900/50">
                <div class="w-10 h-10 bg-emerald-700/30 rounded-lg flex items-center justify-center p-1 shrink-0">
                    <img src="{{ asset('images/logo-kbb.png') }}" alt="Logo KBB" class="w-full h-full object-contain">
                </div>
                <div x-show="sidebarOpen" x-transition.opacity>
                    <h1 class="font-bold text-sm tracking-wide leading-tight">SISTEM SLA</h1>
                    <p class="text-[10px] text-emerald-300 leading-tight uppercase">Admin Dinas</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1 text-sm font-medium">
                <a href="{{ route('perangkat-daerah.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('perangkat-daerah.dashboard') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-chart-pie w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Dashboard</span>
                </a>
                <a href="{{ route('perangkat-daerah.layanandigital') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('perangkat-daerah.layanandigital*') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-globe w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Layanan Digital</span>
                </a>
                <a href="{{ route('perangkat-daerah.ticketing') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('perangkat-daerah.ticketing*') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-ticket w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Ticketing</span>
                </a>
                
                <!-- Menu Kelola Teknisi -->
                <a href="{{ route('perangkat-daerah.teknisi.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('perangkat-daerah.teknisi*') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-users-gear w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Kelola Teknisi</span>
                </a>

                <!-- Menu Evaluasi SLA (Diposisikan rapi di dalam navigasi sidebar) -->
                <a href="{{ route('perangkat-daerah.evaluasi.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('perangkat-daerah.evaluasi*') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-chart-line w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Evaluasi SLA</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer / Logout -->
        <div class="p-4 border-t border-emerald-900/50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-emerald-300 hover:text-white hover:bg-emerald-800/40 rounded-xl transition text-sm">
                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col overflow-y-auto">
        
        <!-- HEADER / NAVBAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-700 text-lg p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="text-lg font-bold text-slate-800">{{ $headerTitle ?? 'Dashboard Perangkat Daerah' }}</h2>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-slate-800">{{ auth()->user()->name ?? 'Admin Dinas' }}</p>
                    <p class="text-[10px] text-slate-500 uppercase font-semibold">Perangkat Daerah</p>
                </div>
                <div class="w-9 h-9 bg-[#052e23] text-amber-400 rounded-full flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                    {{ substr(auth()->user()->name ?? 'AD', 0, 2) }}
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="p-8 space-y-6">
            {{ $slot }}
        </main>
    </div>

    {{ $scripts ?? '' }}
</body>
</html>