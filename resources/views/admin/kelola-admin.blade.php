<x-admin-layout>
    <x-slot name="title">Kelola Admin Dinas - Sistem SLA</x-slot>
    <x-slot name="headerTitle">Kelola Admin Dinas</x-slot>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col gap-1">
        <h1 class="text-2xl font-bold text-slate-800">Manajemen Akun Admin Dinas</h1>
        <p class="text-sm text-slate-500">Pusat pembuatan dan pengelolaan akun admin untuk setiap instansi atau dinas.</p>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Form Tambah Admin Dinas (Dropdown Perangkat Daerah Telah Dihapus) -->
        <div class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800">Tambah Akun Dinas Baru</h3>
            </div>
            
            <form action="{{ route('admin.dinas.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama Dinas / Instansi</label>
                    <input type="text" name="nama_dinas" value="{{ old('nama_dinas') }}" placeholder="Contoh: Dinas Kesehatan KBB" required 
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('nama_dinas') <span class="mt-1 text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama Admin</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required 
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('name') <span class="mt-1 text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Email Resmi</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@dinas.com" required 
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('email') <span class="mt-1 text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nomer WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx" required 
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('whatsapp') <span class="mt-1 text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Penanggung Jawab (PJ)</label>
                    <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" placeholder="Nama Penanggung Jawab" required 
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('penanggung_jawab') <span class="mt-1 text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Password Sementara</label>
                    <input type="password" name="password" placeholder="Min. 6 karakter" required 
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('password') <span class="mt-1 text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full rounded-xl bg-[#064e3b] hover:bg-[#022c22] py-2.5 text-sm font-semibold text-white transition shadow-sm">
                    Simpan & Buat Akun
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Admin Dinas -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2" x-data="{ editModal: false, deleteModal: false, currentAdmin: {} }">
            <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-800">Daftar Admin Dinas Terdaftar</h3>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ count($dinasAdmins) }} Instansi</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-3">No</th>
                            <th class="py-3 px-3">Dinas / Admin</th>
                            <th class="py-3 px-3">Kontak (Email / WA)</th>
                            <th class="py-3 px-3">Penanggung Jawab</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($dinasAdmins as $index => $admin)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-3 text-slate-500 font-medium">{{ $index + 1 }}</td>
                                <td class="py-4 px-3">
                                    <p class="font-bold text-slate-800">{{ $admin->nama_dinas }}</p>
                                    <p class="text-xs text-slate-500 font-medium">{{ $admin->name }}</p>
                                </td>
                                <td class="py-4 px-3">
                                    <p class="text-slate-700">{{ $admin->email }}</p>
                                    <p class="text-xs text-slate-400">WA: {{ $admin->whatsapp }}</p>
                                </td>
                                <td class="py-4 px-3 font-semibold text-slate-700">
                                    {{ $admin->penanggung_jawab }}
                                </td>
                                <td class="py-4 px-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Tombol Edit Trigger Modal -->
                                        <button @click="currentAdmin = {{ json_encode($admin) }}; editModal = true;" 
                                            class="rounded-lg bg-amber-50 p-2 text-amber-600 hover:bg-amber-100 transition" title="Edit Akun">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <!-- Tombol Delete Trigger Modal -->
                                        <button @click="currentAdmin = {{ json_encode($admin) }}; deleteModal = true;" 
                                            class="rounded-lg bg-rose-50 p-2 text-rose-600 hover:bg-rose-100 transition" title="Hapus Akun">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada akun admin dinas yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MODAL EDIT POP-UP -->
            <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak>
                <div @click.away="editModal = false" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-slate-800 text-lg">Edit Akun Admin Dinas</h3>
                        <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>
                    
                    <form :action="'/admin/kelola-dinas/' + currentAdmin.id" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Dinas / Instansi</label>
                            <input type="text" name="nama_dinas" x-model="currentAdmin.nama_dinas" required class="w-full rounded-xl border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Admin</label>
                            <input type="text" name="name" x-model="currentAdmin.name" required class="w-full rounded-xl border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                            <input type="email" name="email" x-model="currentAdmin.email" required class="w-full rounded-xl border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">WhatsApp</label>
                            <input type="text" name="whatsapp" x-model="currentAdmin.whatsapp" required class="w-full rounded-xl border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Penanggung Jawab</label>
                            <input type="text" name="penanggung_jawab" x-model="currentAdmin.penanggung_jawab" required class="w-full rounded-xl border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Password Baru (Opsional)</label>
                            <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full rounded-xl border-slate-200 text-sm">
                        </div>

                        <div class="flex justify-end gap-2 pt-3">
                            <button type="button" @click="editModal = false" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                            <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL DELETE POP-UP -->
            <div x-show="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-cloak>
                <div @click.away="deleteModal = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-center space-y-4">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Hapus Akun Dinas?</h3>
                        <p class="text-sm text-slate-500 mt-1">Apakah kamu yakin ingin menghapus akun <span class="font-bold text-slate-700" x-text="currentAdmin.nama_dinas"></span>? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    
                    <form :action="'/admin/kelola-dinas/' + currentAdmin.id" method="POST" class="flex justify-center gap-3 pt-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="deleteModal = false" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="rounded-xl bg-rose-600 hover:bg-rose-700 px-4 py-2 text-sm font-semibold text-white">Ya, Hapus</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>