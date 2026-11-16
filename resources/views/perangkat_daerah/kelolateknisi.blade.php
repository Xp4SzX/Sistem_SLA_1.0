<x-perangkat-daerah-layout>
    <x-slot name="title">Kelola Teknisi - Perangkat Daerah</x-slot>
    <x-slot name="headerTitle">Manajemen Akun Teknisi</x-slot>

    <div class="space-y-6">
        <!-- Notifikasi Sukses -->
        @if(session('success'))
            <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between">
                <span><i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}</span>
            </div>
        @endif

        <!-- Notifikasi Gagal/Error Validasi General -->
        @if($errors->any())
            <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 rounded-xl text-xs font-semibold flex items-center justify-between">
                <span><i class="fa-solid fa-triangle-exclamation mr-2"></i> Mohon periksa kembali form di bawah ini.</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Tambah Teknisi -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h2 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-plus text-emerald-700"></i> Tambah Teknisi Baru
                </h2>

                <form action="{{ route('perangkat-daerah.teknisi.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Teknisi" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                        @error('name') <span class="text-[10px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Resmi</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="teknisi@dinas.com" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                        @error('email') <span class="text-[10px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                        @error('whatsapp') <span class="text-[10px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Password Sementara</label>
                        <input type="password" name="password" required placeholder="Min. 6 karakter" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                        @error('password') <span class="text-[10px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-[#052e23] text-amber-400 font-bold rounded-lg hover:bg-emerald-900 transition mt-2 shadow-sm">
                        Simpan & Buat Akun
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Teknisi Terdaftar -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base font-bold text-slate-800">Daftar Teknisi Terdaftar</h2>
                    <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">
                        {{ isset($technicians) ? $technicians->count() : 0 }} Orang
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 bg-slate-50">
                                <th class="py-3 px-3 w-10 text-center">No</th>
                                <th class="py-3 px-4">Nama & Kontak</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($technicians ?? [] as $index => $tech)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3 text-center font-medium">{{ $index + 1 }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-bold block text-slate-900">{{ $tech->name }}</span>
                                    <span class="text-[11px] text-slate-500">WA: {{ $tech->whatsapp_number ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4">{{ $tech->email }}</td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('perangkat-daerah.teknisi.destroy', $tech->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun teknisi ini?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold transition p-1.5" title="Hapus Teknisi">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-slate-400">Belum ada akun teknisi terdaftar di instansi ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-perangkat-daerah-layout>