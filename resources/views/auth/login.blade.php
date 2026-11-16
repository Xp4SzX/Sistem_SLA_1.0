<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - SLA Helpdesk</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kbb.png') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-emerald-50 to-teal-100 min-h-screen flex items-center justify-center p-4">

    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-emerald-50 max-w-5xl w-full flex flex-col md:flex-row overflow-hidden min-h-[650px]">
        
        <!-- SISI KIRI: Informasi & Branding -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-between bg-white relative">
            <div>
                <!-- Header Logo -->
                <div class="flex items-center space-x-3 mb-8">
                    <img src="{{ asset('images/logo-kbb.png') }}" alt="Logo Pemkab Bandung Barat" class="h-12 w-auto drop-shadow-sm">
                    <div>
                        <h1 class="font-bold text-slate-800 leading-tight text-lg">SLA Helpdesk</h1>
                        <p class="text-xs text-emerald-600 font-semibold tracking-wide">Pemkab Bandung Barat</p>
                    </div>
                </div>

                <!-- Judul Utama -->
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-4 leading-tight">
                    Sistem Layanan &<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">
                        Pengaduan Terpadu
                    </span>
                </h2>
                
                <!-- Deskripsi -->
                <p class="text-slate-600 font-medium text-sm md:text-base mb-8 leading-relaxed">
                    Pelayanan Publik Transparan, Cepat, dan Terukur untuk Masyarakat Kabupaten Bandung Barat.
                </p>

                <!-- Ilustrasi -->
                <div class="flex justify-center my-6">
                    <img src="{{ asset('images/illustration.png') }}" alt="Ilustrasi Layanan" class="max-w-xs w-full h-auto object-contain">
                </div>

                <!-- Fitur Utama -->
                <div class="space-y-3.5 mt-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-xs font-bold">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="font-semibold text-slate-700 text-sm">Pantau Status Real-Time</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-xs font-bold">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="font-semibold text-slate-700 text-sm">Penanganan Berstandar SLA</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-xs font-bold">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="font-semibold text-slate-700 text-sm">Respon Cepat OPD</span>
                    </div>
                </div>
            </div>

            <!-- Footer Kiri -->
            <div class="mt-8 text-xs text-slate-400 font-medium">
                © Diskominfo Kabupaten Bandung Barat
            </div>
        </div>

        <!-- SISI KANAN: Form Login -->
        <div class="w-full md:w-1/2 bg-slate-50/70 p-8 md:p-12 flex flex-col justify-center border-t md:border-t-0 md:border-l border-slate-100 relative">
            
            <div class="max-w-md w-full mx-auto">
                <!-- Tombol Kembali ke Beranda -->
                <div class="mb-6">
                    <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-500 hover:text-emerald-600 transition group">
                        <i class="fa-solid fa-arrow-left text-[11px] group-hover:-translate-x-1 transition-transform"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>

                <h2 class="text-2xl font-extrabold text-slate-800 text-center mb-1">Selamat Datang</h2>
                <p class="text-xs text-slate-500 text-center font-medium mb-6">Masuk untuk mengakses layanan helpdesk</p>

                <!-- Toggle Tab (Masuk / Register) -->
                <div class="bg-slate-200/70 p-1.5 rounded-xl flex mb-6">
                    <button class="w-1/2 text-center py-2 text-xs font-bold text-emerald-600 bg-white rounded-lg shadow-sm">
                        Masuk
                    </button>
                    <a href="{{ route('register') }}" class="w-1/2 text-center py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                        Register
                    </a>
                </div>

                
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs font-semibold">
                        <i class="fa-solid fa-circle-exclamation mr-1.5"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Form Login -->
                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- E-mail / NIK -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">E-mail / NIK</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 group-focus-within:text-emerald-600 text-sm transition-colors">
                                <i class="fa-regular fa-user"></i>
                            </span>
                            <input type="text" 
                                   name="login" 
                                   required 
                                   placeholder="E-mail atau NIK" 
                                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Password</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 group-focus-within:text-emerald-600 text-sm transition-colors">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" 
                                   name="password" 
                                   required 
                                   placeholder="Password" 
                                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-2 flex justify-center">
                        <button type="submit" class="w-full sm:w-2/3 py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl text-xs tracking-wide transition duration-200 shadow-md shadow-emerald-500/20 active:scale-[0.98]">
                            Masuk
                        </button>
                    </div>
                </form>

                <!-- Pemisah / Divider -->
                <div class="relative my-6 flex items-center justify-center">
                    <div class="border-t border-slate-200 w-full"></div>
                    <span class="bg-slate-50 px-3 text-[11px] font-semibold text-slate-400 absolute">atau</span>
                </div>

                <!-- OPSI MASUK SEBAGAI ADMIN -->
                <div class="text-center">
                    <a href="{{ url('/admin/login') }}" class="inline-flex items-center justify-center space-x-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 py-2.5 px-4 rounded-xl w-full sm:w-2/3 transition duration-200">
                        <i class="fa-solid fa-user-shield text-sm"></i>
                        <span>Masuk sebagai Admin</span>
                    </a>
                </div>

            </div>

        </div>
    </div>

</body>
</html>