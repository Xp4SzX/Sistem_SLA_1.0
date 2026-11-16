<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - SLA Helpdesk</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kbb.png') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-['Inter'] bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden text-slate-200">

    <!-- Ambient Glow BG Background -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-[500px] pointer-events-none -z-10 overflow-hidden">
        <div class="absolute top-10 left-10 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Card Container -->
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-emerald-900/50 max-w-md w-full p-8 md:p-10 relative z-10">
        
        <!-- Navigation Back to Portal -->
        <div class="mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-400 hover:text-emerald-400 transition group">
                <i class="fa-solid fa-arrow-left text-[11px] group-hover:-translate-x-1 transition-transform"></i>
                <span>Kembali ke Portal Utama</span>
            </a>
        </div>

        <!-- Header Branding Administrator -->
        <div class="text-center space-y-3 mb-8">
            <div class="inline-flex items-center justify-center p-3 bg-emerald-950/80 border border-emerald-800/60 rounded-2xl shadow-inner mb-2">
                <img src="{{ asset('images/logo-kbb.png') }}" alt="Logo KBB" class="h-12 w-auto drop-shadow">
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-wide">Portal Administrator</h1>
                <p class="text-xs text-emerald-400 font-medium mt-1">SLA Helpdesk Diskominfo KBB</p>
            </div>
        </div>

        <!-- Alert Error (Opsional Bawaan Laravel) -->
        @if ($errors->any())
            <div class="mb-6 p-3.5 bg-rose-950/80 border border-rose-800/80 rounded-xl text-rose-300 text-xs">
                <div class="flex items-center space-x-2 font-bold mb-1">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Akses Ditolak</span>
                </div>
                <ul class="list-disc pl-4 space-y-0.5 text-[11px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Login Admin -->
        <form action="{{ route('admin.login') }}" method="POST" class="space-y-5" x-data="{ showPassword: false }">
            @csrf

            <!-- Input Username / NIP / Email Admin -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Username / NIP Administrator</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 group-focus-within:text-emerald-400 text-sm transition-colors">
                        <i class="fa-solid fa-user-shield"></i>
                    </span>
                    <input type="text" 
                           name="username" 
                           required 
                           placeholder="Masukkan Username / NIP" 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-xs font-medium text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                </div>
            </div>

            <!-- Input Password Admin dengan Toggle View -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Kata Sandi</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 group-focus-within:text-emerald-400 text-sm transition-colors">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           required 
                           placeholder="••••••••" 
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-xs font-medium text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    
                    <!-- Tombol Toggle Show/Hide Password -->
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-500 hover:text-emerald-400 text-xs transition-colors">
                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me & Info Tambahan -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 bg-slate-950 border-slate-800 rounded focus:ring-emerald-500 focus:ring-offset-slate-900 accent-emerald-600">
                    <span class="ml-2 text-[11px] font-medium text-slate-400">Ingat Sesi Ini</span>
                </label>
                <span class="text-[11px] font-semibold text-emerald-400/80 bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-800/40">
                    Sistem Internal
                </span>
            </div>

            <!-- Tombol Submit Login -->
            <div class="pt-3">
                <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold rounded-xl text-xs tracking-wider transition duration-200 shadow-lg shadow-emerald-950/50 active:scale-[0.98] flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </div>
        </form>

        <!-- Footer Card -->
        <div class="mt-8 pt-6 border-t border-slate-800/80 text-center text-[11px] text-slate-500">
            Halaman terbatas. Hanya dapat diakses oleh tim teknis & petugas resmi OPD Diskominfo KBB.
        </div>

    </div>

</body>
</html>