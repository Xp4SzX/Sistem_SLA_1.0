<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Admin - Sistem SLA' }}</title>
    
    <!-- FAVICON LOGO KBB -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kbb.png') }}">
    
    <!-- Tailwind CSS & JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{ $styles ?? '' }}
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-800 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true, profileDropdown: false }">

    <!-- SIDEBAR -->
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
                    <p class="text-[10px] text-emerald-300 leading-tight">Fasilitas Dukungan & Monitoring</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1 text-sm font-medium">
                <!-- Dashboard (Semua Role Bisa Akses) -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-house w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Dashboard</span>
                </a>

                <!-- Ticketing (Semua Role Bisa Akses) -->
                <a href="{{ route('admin.ticketing') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.ticketing*') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-ticket w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Ticketing</span>
                </a>

                <!-- SLA Monitoring (Hanya Admin Pusat / Bukan Admin Dinas) -->
                @if(auth()->check() && auth()->user()->role !== 'admin_dinas')
                <a href="{{ route('admin.sla-monitoring') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.sla-monitoring') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-chart-line w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>SLA Monitoring</span>
                </a>        
                @endif

                <!-- Layanan Digital (Semua Role Bisa Akses) -->
                <a href="{{ route('admin.layanandigital') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.layanandigital*') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-cubes w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Layanan Digital</span>
                </a>

                <!-- Evaluasi & Laporan (Hanya Admin Pusat) -->
                @if(auth()->check() && auth()->user()->role !== 'admin_dinas')
                <a href="{{ route('admin.evaluasi') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.evaluasi') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-user-check w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Evaluasi & Laporan</span>
                </a>
                @endif

                <!-- Kelola Admin Dinas (Hanya Admin Pusat) -->
                @if(auth()->check() && auth()->user()->role !== 'admin_dinas')
                <a href="{{ route('admin.dinas.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dinas.*') ? 'bg-emerald-800/60 text-amber-400 border border-emerald-600/30 font-semibold shadow-sm' : 'text-emerald-100 hover:bg-emerald-800/40 hover:text-white' }} rounded-xl transition">
                    <i class="fa-solid fa-users-gear w-5"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Kelola Admin Dinas</span>
                </a>
                @endif
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
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-700 text-lg p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="text-lg font-bold text-slate-800">{{ $headerTitle ?? 'Dashboard Admin' }}</h2>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-slate-600">{{ auth()->user()->name ?? 'Admin' }}</span>
                <div class="w-9 h-9 bg-[#052e23] text-white rounded-full flex items-center justify-center font-bold text-xs uppercase">
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