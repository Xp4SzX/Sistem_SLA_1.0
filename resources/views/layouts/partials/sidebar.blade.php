<aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-[#022C22] text-white flex flex-col justify-between transition-all duration-300 ease-in-out relative z-30 shrink-0">
    <div>
<!-- Brand Header -->
<div class="p-5 flex items-center gap-3 border-b border-emerald-900/50">
    <!-- Ganti bagian kotak ikon lama dengan tag img logo kbb -->
    <div class="w-10 h-10 bg-emerald-700/30 rounded-lg flex items-center justify-center p-1 shrink-0">
        <img src="{{ asset('images/logo-kbb.png') }}" alt="Logo KBB" class="w-full h-full object-contain">
    </div>
    
    <div x-show="sidebarOpen" x-transition.opacity>
        <h1 class="font-bold text-sm tracking-wide leading-tight">SISTEM SLA</h1>
        <p class="text-[10px] text-emerald-300 leading-tight">Fasilitas Dukungan & Monitoring</p>
    </div>
</div>

        <!-- Navigation Links -->
        <nav class="p-3 space-y-1.5">
            @php
    $menus = [
        ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Dashboard', 'active' => request()->routeIs('dashboard')],
        ['route' => 'user.ticketing', 'icon' => '🎫', 'label' => 'Tiket Saya', 'active' => request()->routeIs('user.ticketing')],
        ['route' => 'create-ticket', 'icon' => '➕', 'label' => 'Buat Tiket', 'active' => request()->routeIs('create-ticket') || request()->routeIs('tickets.create')],
        ['route' => 'sla-sop', 'icon' => '📄', 'label' => 'SLA & SOP', 'active' => request()->routeIs('sla-sop')],
        ['route' => 'profile', 'icon' => '👤', 'label' => 'Profile', 'active' => request()->routeIs('profile')],
    ];
@endphp

            @foreach($menus as $menu)
                <a href="{{ route($menu['route']) }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ $menu['active'] ? 'bg-emerald-900/90 border border-[#F59E0B]/40 text-[#F59E0B] font-semibold' : 'text-emerald-200/70 hover:text-white hover:bg-emerald-900/50' }}">
                    <span class="text-xl">{{ $menu['icon'] }}</span>
                    <span x-show="sidebarOpen" x-transition.opacity class="text-sm whitespace-nowrap">{{ $menu['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Logout -->
    <div class="p-3 border-t border-emerald-900/60">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-slate-800/60 transition-all text-left">
                <span class="text-xl">🚪</span>
                <span x-show="sidebarOpen" x-transition.opacity class="text-sm whitespace-nowrap">Keluar</span>
            </button>
        </form>
    </div>
</aside>