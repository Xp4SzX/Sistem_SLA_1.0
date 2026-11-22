<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Teknisi - Sistem SLA' }}</title>
    
    <!-- Tailwind CSS & FontAwesome -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kbb.png') }}">
    {{ $scripts ?? '' }}
</head>
<body class="bg-slate-100 font-sans antialiased">

    <div class="min-h-screen flex">
        <!-- SIDEBAR KHUSUS TEKNISI -->
        <aside class="w-64 bg-[#052e23] text-white flex flex-col justify-between hidden md:flex shrink-0 shadow-lg">
            <div>
                <!-- Logo & Brand -->
                <div class="p-6 border-b border-emerald-900/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-400 flex items-center justify-center font-bold text-slate-900 shadow">
                        <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-sm tracking-wide">SISTEM SLA</h1>
                        <span class="text-[10px] text-amber-400 font-semibold tracking-wider uppercase">Teknisi Dinas</span>
                    </div>
                </div>

                <!-- Navigation Menu (Hanya Dashboard & Ticketing) -->
                <nav class="p-4 space-y-1.5 text-xs font-medium">
                    <a href="{{ route('teknisi.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('teknisi.dashboard') ? 'bg-amber-400 text-slate-900 font-bold shadow' : 'text-emerald-100 hover:bg-emerald-950/60' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
                    </a>
                    
                    <a href="{{ route('teknisi.ticketing') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('teknisi.ticketing*') ? 'bg-amber-400 text-slate-900 font-bold shadow' : 'text-emerald-100 hover:bg-emerald-950/60' }}">
                        <i class="fa-solid fa-ticket w-5 text-center"></i> Ticketing
                    </a>
                </nav>
            </div>

            <!-- Tombol Keluar / Logout -->
            <div class="p-4 border-t border-emerald-900/50">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-rose-300 hover:bg-rose-950/50 hover:text-rose-200 transition font-medium text-xs">
                        <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Navbar -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-4">
                    <h2 class="text-sm font-bold text-slate-800">{{ $headerTitle ?? 'Dashboard Teknisi' }}</h2>
                </div>
                
                <!-- User Profile Badge -->
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <span class="block text-xs font-bold text-slate-800">{{ auth()->user()->name ?? 'Teknisi' }}</span>
                        <span class="block text-[10px] text-slate-500 uppercase">Teknisi Dinas</span>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-[#052e23] text-amber-400 font-bold flex items-center justify-center text-xs shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'T', 0, 2)) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 space-y-6 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

</body>
</html>