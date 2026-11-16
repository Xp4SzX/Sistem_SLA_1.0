<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - SLA Helpdesk Pemkab Bandung Barat</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kbb.png') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-['Inter'] bg-gradient-to-br from-slate-100 via-emerald-50/50 to-teal-100/60 text-slate-800 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- DEKORASI BACKGROUND GLOW -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[600px] pointer-events-none -z-10 overflow-hidden">
        <div class="absolute -top-24 -left-20 w-96 h-96 bg-emerald-300/30 rounded-full blur-3xl"></div>
        <div class="absolute top-40 -right-20 w-96 h-96 bg-teal-300/30 rounded-full blur-3xl"></div>
    </div>

    <!-- NAVBAR HEADER -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 shadow-sm transition">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3 group hover:opacity-90 transition">
                <img src="{{ asset('images/logo-kbb.png') }}" alt="Logo KBB" class="h-9 w-auto">
                <div class="flex flex-col">
                    <span class="font-bold text-slate-800 text-base leading-none group-hover:text-emerald-600 transition">SLA Helpdesk</span>
                    <span class="text-[10px] text-emerald-600 font-semibold tracking-wider">Pemkab Bandung Barat</span>
                </div>
            </a>

            <!-- Navigation Menu -->
            <nav class="flex items-center space-x-6 text-xs font-semibold">
                <a href="{{ url('/') }}" class="text-emerald-600 font-bold border-b-2 border-emerald-600 py-5">Beranda</a>
                <div class="h-4 w-px bg-slate-300"></div>
                <a href="{{ route('login') }}" class="text-slate-600 hover:text-emerald-600 transition">Login</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('register') }}" class="text-slate-600 hover:text-emerald-600 transition">Daftar</a>
            </nav>
        </div>
    </header>

    <!-- CONTENT WRAPPER -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:24px_24px] bg-[radial-gradient-opacity-10] rounded-3xl my-2">

        <!-- HERO BANNER SECTION -->
        <section class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 md:p-8 border border-emerald-100/80 flex flex-col md:flex-row items-center gap-8 shadow-lg shadow-emerald-900/5">
            <div class="w-full md:w-1/2 bg-white rounded-2xl border border-emerald-200/60 shadow-sm overflow-hidden h-64 md:h-80 flex items-center justify-center">
                <img src="{{ asset('images/kantor.png') }}" alt="Pelayanan Publik" class="w-full h-full object-cover">
            </div>

            <div class="w-full md:w-1/2 space-y-3">
                <span class="inline-block px-3 py-1 bg-emerald-100/80 text-emerald-700 text-[11px] font-bold rounded-full tracking-wide">
                    Layanan Resmi Pemkab Bandung Barat
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight">
                    Pelayanan Publik<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">
                        Kab. Bandung Barat
                    </span>
                </h1>
                <p class="text-xs md:text-sm text-slate-600 leading-relaxed font-medium">
                    Platform helpdesk terintegrasi untuk memantau, melaporkan, dan menyelesaikan kendala layanan digital dengan standar SLA yang transparan dan akuntabel.
                </p>
            </div>
        </section>

        <!-- KARTU STATISTIK DINAMIS -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-300 hover:shadow-md transition duration-200">
                <div class="text-slate-400 text-xs font-semibold">Total Pengaduan</div>
                <div class="text-2xl font-extrabold text-slate-800 mt-2">{{ number_format($totalPengaduan ?? 0) }}</div>
            </div>
            <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-300 hover:shadow-md transition duration-200">
                <div class="text-slate-400 text-xs font-semibold">Dalam Proses</div>
                <div class="text-2xl font-extrabold text-amber-500 mt-2">{{ number_format($dalamProses ?? 0) }}</div>
            </div>
            <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-300 hover:shadow-md transition duration-200">
                <div class="text-slate-400 text-xs font-semibold">Selesai</div>
                <div class="text-2xl font-extrabold text-emerald-600 mt-2">{{ number_format($selesai ?? 0) }}</div>
            </div>
            <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-300 hover:shadow-md transition duration-200">
                <div class="text-slate-400 text-xs font-semibold">Capaian SLA</div>
                <div class="text-2xl font-extrabold text-teal-600 mt-2">{{ $capaianSla ?? '100%' }}</div>
            </div>
        </section>

        <!-- LAYANAN DIGITAL DINAMIS DENGAN FITUR LIHAT LEBIH BANYAK -->
        <section x-data="{ showAll: false }" class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-2">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800">Layanan Digital</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Buat tiket dukungan langsung untuk kategori layanan yang Anda butuhkan</p>
                </div>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-100/70 px-3 py-1 rounded-full border border-emerald-200 self-start md:self-auto"
                      x-text="showAll ? 'Menampilkan Semua ' + {{ count($services ?? []) }} + ' Layanan' : 'Menampilkan 6 dari ' + {{ count($services ?? []) }} + ' Layanan'">
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($services ?? [] as $index => $service)
                    <div @if($index >= 6) x-show="showAll" x-cloak x-transition @endif
                         class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition group flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-100/80 text-emerald-700 flex items-center justify-center text-xl mb-4 group-hover:bg-emerald-600 group-hover:text-white transition">
                                @if($service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover rounded-xl">
                                @else
                                    <i class="fa-solid fa-layer-group"></i>
                                @endif
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-2">{{ $service->name }}</h3>
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">{{ $service->description ?? 'Layanan terpadu Pemkab Bandung Barat.' }}</p>
                            <div class="mt-3 inline-block px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-[10px] font-semibold">
                                SLA: {{ $service->jangka_waktu ?? '8 Jam' }}
                            </div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 font-medium uppercase">{{ $service->service_code ?? 'LAYANAN' }}</span>
                            <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700 space-x-1.5">
                                <span>Buat Tiket</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-slate-400 font-medium bg-white/50 rounded-2xl border border-slate-200">
                        <i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
                        Belum ada data layanan digital yang aktif. Silakan tambahkan layanan melalui Panel Admin.
                    </div>
                @endforelse
            </div>

            <!-- TOMBOL LIHAT LEBIH BANYAK / SEMBUNYIKAN -->
            @if(isset($services) && count($services) > 6)
                <div class="pt-4 flex justify-center">
                    <button @click="showAll = !showAll" 
                            class="inline-flex items-center space-x-2 bg-white/90 hover:bg-emerald-50 text-emerald-700 border border-emerald-200 px-6 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 shadow-sm hover:shadow active:scale-95">
                        <span x-text="showAll ? 'Sembunyikan Sebagian' : 'Lihat ' + ({{ count($services) }} - 6) + ' Layanan Lainnya'"></span>
                        <i class="fa-solid" :class="showAll ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </div>
            @endif
        </section>

        <!-- ALUR PENANGANAN TIKET -->
        <section class="py-8 bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-slate-200/60 shadow-sm">
            <div class="text-center max-w-xl mx-auto mb-10">
                <h2 class="text-xl font-extrabold text-slate-800">Alur Penanganan Tiket</h2>
                <p class="text-xs text-slate-500 mt-1">Proses transparan dari laporan hingga penyelesaian</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-4 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-full flex items-center justify-center relative mb-4">
                        <div class="w-20 h-20 rounded-2xl bg-emerald-100/90 text-emerald-700 flex items-center justify-center text-3xl font-bold shadow-sm z-10">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div class="hidden md:block absolute -right-1/2 top-1/2 -translate-y-1/2 w-full text-slate-400 text-xl z-0">
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </div>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800 mb-1">Buat Tiket</h4>
                    <p class="text-[11px] text-slate-500 leading-relaxed max-w-[190px]">Isi formulir laporan dengan detail masalah dan layanan terkait</p>
                </div>

                <div class="flex flex-col items-center">
                    <div class="w-full flex items-center justify-center relative mb-4">
                        <div class="w-20 h-20 rounded-2xl bg-emerald-100/90 text-emerald-700 flex items-center justify-center text-3xl font-bold shadow-sm z-10">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <div class="hidden md:block absolute -right-1/2 top-1/2 -translate-y-1/2 w-full text-slate-400 text-xl z-0">
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </div>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800 mb-1">Verifikasi</h4>
                    <p class="text-[11px] text-slate-500 leading-relaxed max-w-[190px]">Tim helpdesk memverifikasi dan mengkategorikan tiket Anda</p>
                </div>

                <div class="flex flex-col items-center">
                    <div class="w-full flex items-center justify-center relative mb-4">
                        <div class="w-20 h-20 rounded-2xl bg-emerald-100/90 text-emerald-700 flex items-center justify-center text-3xl font-bold shadow-sm z-10">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <div class="hidden md:block absolute -right-1/2 top-1/2 -translate-y-1/2 w-full text-slate-400 text-xl z-0">
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </div>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800 mb-1">Penanganan & SLA</h4>
                    <p class="text-[11px] text-slate-500 leading-relaxed max-w-[190px]">Teknisi ditugaskan dan proses penanganan dimulai sesuai SLA</p>
                </div>

                <div class="flex flex-col items-center">
                    <div class="w-full flex items-center justify-center relative mb-4">
                        <div class="w-20 h-20 rounded-2xl bg-emerald-100/90 text-emerald-700 flex items-center justify-center text-3xl font-bold shadow-sm z-10">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800 mb-1">Selesai</h4>
                    <p class="text-[11px] text-slate-500 leading-relaxed max-w-[190px]">Masalah terselesaikan dan pemohon memberi konfirmasi</p>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER INFORMASI -->
    <footer class="bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 text-slate-300 mt-16 pt-12 pb-8 border-t border-emerald-900/50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-10 text-xs">
            
            <!-- Kolom 1: Branding -->
            <div class="space-y-4">
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                    <div class="bg-white/10 p-1.5 rounded-lg backdrop-blur-sm border border-white/10">
                        <img src="{{ asset('images/logo-kbb.png') }}" alt="Logo KBB" class="h-8 w-auto">
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-white text-sm tracking-wide">SLA Helpdesk</span>
                        <span class="text-[10px] text-emerald-400 font-semibold tracking-wider">Pemkab Bandung Barat</span>
                    </div>
                </a>
                <p class="text-slate-400 leading-relaxed pr-4">
                    Sistem Layanan & Pengaduan Terpadu Dinas Komunikasi, Informatika, dan Statistik Kabupaten Bandung Barat.
                </p>
            </div>

            <!-- Kolom 2: Kontak -->
            <div class="space-y-3">
                <h5 class="font-bold text-white text-sm border-b border-emerald-800/60 pb-2 w-fit pr-6">Kontak Kami</h5>
                <ul class="space-y-2.5 text-slate-300 leading-relaxed">
                    <li class="flex items-start space-x-2.5">
                        <i class="fa-solid fa-location-dot text-emerald-400 mt-0.5 text-sm"></i>
                        <span>Jl. Raya Padalarang – Cisarua KM 2, Desa Mekarsari, Kecamatan Ngamprah, Kabupaten Bandung Barat, Jawa Barat 40552</span>
                    </li>
                    <li class="flex items-center space-x-2.5">
                        <i class="fa-solid fa-phone text-emerald-400 text-sm"></i>
                        <span>0877-2550-0060</span>
                    </li>
                    <li class="flex items-center space-x-2.5">
                        <i class="fa-solid fa-envelope text-emerald-400 text-sm"></i>
                        <a href="mailto:setda@bandungbaratkab.go.id" class="text-emerald-400 hover:text-emerald-300 underline transition">setda@bandungbaratkab.go.id</a>
                    </li>
                </ul>
            </div>

            <!-- Kolom 3: Jam Pelayanan -->
            <div class="space-y-3">
                <h5 class="font-bold text-white text-sm border-b border-emerald-800/60 pb-2 w-fit pr-6">Jam Pelayanan</h5>
                <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-3.5 space-y-2">
                    <div class="flex justify-between border-b border-slate-700/60 pb-1.5">
                        <span class="text-slate-400">Senin - Kamis</span>
                        <span class="font-semibold text-emerald-400">08:00 - 16:00 WIB</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-700/60 pb-1.5">
                        <span class="text-slate-400">Jumat</span>
                        <span class="font-semibold text-emerald-400">08:00 - 15:00 WIB</span>
                    </div>
                    <div class="flex justify-between pt-0.5">
                        <span class="text-rose-400 font-semibold">Sabtu / Minggu</span>
                        <span class="text-rose-400 font-semibold">Tutup</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Copyright -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pt-6 border-t border-slate-800/80 text-center text-[11px] text-slate-500">
            © {{ date('Y') }} Diskominfo Kabupaten Bandung Barat. All Rights Reserved.
        </div>
    </footer>

</body>
</html>