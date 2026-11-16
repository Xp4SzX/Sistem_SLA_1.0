<x-app-layout>
    <x-slot name="title">Buat Tiket Laporan & Layanan - SLA Helpdesk</x-slot>
    <x-slot name="headerTitle">Buat Tiket Laporan & Pengajuan Layanan</x-slot>

    <!-- MAIN BODY / CONTENT -->
    <main
        class="flex-1 overflow-y-auto p-6 bg-[#F8FAFC]"
        x-data="{
            currentStep: 1,
            
            // Data Perangkat Daerah dan Layanan dari Controller
            departmentsData: @js($departments), 
            selectedDepartment: @js(old('department_id', '')),
            
            servicesData: @js($services),
            selectedService: @js(old('service_id', '')),
            
            // Filter Layanan berdasarkan nama perangkat daerah yang dipilih
            get filteredServices() {
                if (!this.selectedDepartment) return [];
                let dept = this.departmentsData.find(d => d.id == this.selectedDepartment);
                if (!dept) return [];
                
                return this.servicesData.filter(s => 
                    s.department_name && s.department_name.toLowerCase().trim() === dept.nama_dinas.toLowerCase().trim()
                );
            },

            // Getter untuk mengambil data layanan yang terpilih
            get activeService() {
                return this.servicesData.find(s => s.id == this.selectedService) || {};
            },

            selectedCategory: @js(old('category', 'backend')),
            ticketTitle: @js(old('title', '')),
            priority: @js(old('priority', 'medium')),
            description: @js(old('description', '')),
        }"
    >
        <form
            method="POST"
            action="{{ route('tickets.store') }}"
            enctype="multipart/form-data"
        >
            @csrf
            
            <!-- INPUT TERSEMBUNYI UNTUK MENGATASI ERROR REQUEST TYPE -->
            <input type="hidden" name="request_type" value="kendala_teknis">

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="mb-2 font-bold">Periksa kembali data berikut:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- STEPPER -->
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="relative mx-auto flex max-w-3xl items-center justify-between">
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold transition-all"
                            :class="currentStep >= 1 ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-500'">
                            1
                        </div>
                        <span class="mt-2 text-xs font-bold text-slate-800">Perangkat Daerah & Layanan</span>
                    </div>

                    <div class="relative -top-3 mx-4 h-0.5 flex-1 bg-slate-200">
                        <div class="h-full bg-slate-900 transition-all" :style="currentStep > 1 ? 'width: 100%' : 'width: 0%'"></div>
                    </div>

                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold transition-all"
                            :class="currentStep >= 2 ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-500'">
                            2
                        </div>
                        <span class="mt-2 text-xs font-bold text-slate-800">Detail Kendala</span>
                    </div>

                    <div class="relative -top-3 mx-4 h-0.5 flex-1 bg-slate-200">
                        <div class="h-full bg-slate-900 transition-all" :style="currentStep > 2 ? 'width: 100%' : 'width: 0%'"></div>
                    </div>

                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold transition-all"
                            :class="currentStep >= 3 ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-500'">
                            3
                        </div>
                        <span class="mt-2 text-xs font-bold text-slate-800">Konfirmasi</span>
                    </div>
                </div>
            </div>

            <!-- FORM CONTENT -->
            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">

                <!-- STEP 1 -->
                <section x-show="currentStep === 1" class="space-y-8">
                    <!-- PILIH PERANGKAT DAERAH -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1">Pilih Perangkat Daerah / OPD</h3>
                        <p class="text-xs font-medium text-slate-500 mb-3">Pilih instansi atau perangkat daerah tujuan pelaporan</p>

                        <select
                            name="department_id"
                            x-model="selectedDepartment"
                            @change="selectedService = ''"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-medium text-slate-800 focus:border-[#F59E0B] focus:outline-none"
                        >
                            <option value="">-- Pilih Perangkat Daerah --</option>
                            <template x-for="dept in departmentsData" :key="dept.id">
                                <option :value="dept.id" x-text="dept.nama_dinas"></option>
                            </template>
                        </select>
                    </div>

                    <!-- PILIH LAYANAN TERKAIT -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Pilih Layanan Terkait</h3>
                        <p class="mt-1 text-xs font-medium text-slate-500">Daftar layanan yang tersedia pada perangkat daerah terpilih</p>

                        <div class="mt-4" x-show="!selectedDepartment">
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-700">
                                Silakan pilih Perangkat Daerah terlebih dahulu untuk melihat daftar layanan yang tersedia.
                            </div>
                        </div>

                        <div class="mt-4" x-show="selectedDepartment && filteredServices.length === 0">
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-700">
                                Belum ada layanan aktif yang tersedia untuk Perangkat Daerah ini.
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3" x-show="selectedDepartment && filteredServices.length > 0">
                            <template x-for="service in filteredServices" :key="service.id">
                                <button
                                    type="button"
                                    @click="selectedService = service.id"
                                    class="flex h-32 flex-col items-center justify-center gap-2 rounded-2xl border p-4 text-center transition-all"
                                    :class="selectedService == service.id ? 'border-slate-900 bg-slate-50 ring-2 ring-slate-900/10' : 'border-slate-200 bg-white hover:border-slate-400'"
                                >
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                        <i class="fa-solid fa-server"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 line-clamp-2" x-text="service.name"></span>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="service_id" x-model="selectedService">
                    </div>

                    <!-- KATEGORI KENDALA -->
                    <div>
                        <h4 class="mb-3 text-sm font-bold text-slate-900">Kategori Kendala</h4>

                        <div class="flex flex-wrap gap-3">
                            @foreach ([
                                'backend' => 'Backend / Sistem',
                                'ui_ux' => 'Tampilan UI/UX',
                                'network' => 'Jaringan (Network)',
                                'hardware' => 'Perangkat / Hardware',
                                'database' => 'Database',
                                'security' => 'Keamanan (Security)',
                                'lainnya' => 'Lainnya'
                            ] as $value => $label)
                                <button
                                    type="button"
                                    @click="selectedCategory = @js($value)"
                                    class="rounded-xl border px-4 py-2 text-xs transition-all"
                                    :class="selectedCategory === @js($value) ? 'border-slate-900 bg-slate-50 font-bold text-slate-900' : 'border-slate-200 bg-white font-medium text-slate-600 hover:border-slate-400'"
                                >
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="category" x-model="selectedCategory">
                    </div>

                    <div class="flex justify-start pt-4">
                        <button
                            type="button"
                            @click="currentStep = 2"
                            :disabled="!selectedDepartment || !selectedService"
                            class="rounded-xl bg-slate-900 px-6 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                        >
                            Lanjutkan
                        </button>
                    </div>
                </section>

                <!-- STEP 2 -->
                <section x-show="currentStep === 2" x-cloak class="space-y-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Detail Kendala dan Masalah</h3>
                            <p class="mt-1 text-xs font-medium text-slate-500" x-text="'Layanan terkait: ' + (activeService.name || '-')"></p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-bold text-slate-700">Judul Tiket / Subjek Laporan</label>
                            <input
                                type="text"
                                name="title"
                                x-model="ticketTitle"
                                maxlength="150"
                                placeholder="Contoh: Tidak bisa login setelah ubah password"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs focus:border-[#F59E0B] focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-bold text-slate-700">Tingkat Prioritas</label>
                            <select
                                name="priority"
                                x-model="priority"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-medium focus:border-[#F59E0B] focus:outline-none"
                            >
                                <option value="low">Low - Normal / Pertanyaan umum</option>
                                <option value="medium">Medium - Standar operasional</option>
                                <option value="high">High - Penting / Mendesak</option>
                                <option value="urgent">Urgent - Sangat penting / Kritis</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-bold text-slate-700">Deskripsi Detail Kendala / Error</label>
                            <textarea
                                name="description"
                                x-model="description"
                                rows="5"
                                placeholder="Jelaskan kronologi kejadian dan pesan error..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs focus:border-[#F59E0B] focus:outline-none"
                            ></textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-bold text-slate-700">Lampiran Dokumen / Screenshot (Opsional)</label>
                            <input
                                type="file"
                                name="attachment"
                                accept=".png,.jpg,.jpeg,.pdf,.doc,.docx"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600"
                            >
                            <p class="mt-1 text-[10px] text-slate-400">Format PNG, JPG, PDF, atau DOCX maksimal 5 MB.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <button
                            type="button"
                            @click="currentStep = 1"
                            class="rounded-xl bg-slate-100 px-6 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-200"
                        >
                            Kembali
                        </button>
                        <button
                            type="button"
                            @click="currentStep = 3"
                            class="rounded-xl bg-slate-900 px-6 py-3 text-xs font-bold text-white transition hover:bg-slate-700"
                        >
                            Lanjutkan ke Konfirmasi
                        </button>
                    </div>
                </section>

                <!-- STEP 3: KONFIRMASI -->
                <section x-show="currentStep === 3" x-cloak class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Konfirmasi Permintaan & Laporan</h3>
                        <p class="mt-1 text-xs font-medium text-slate-500">Periksa kembali data sebelum dikirimkan ke operator perangkat daerah</p>
                    </div>

                    <div class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-xs">
                        <div class="flex justify-between gap-4 border-b border-slate-200 pb-3">
                            <span class="text-slate-500">Perangkat Daerah</span>
                            <span class="text-right font-bold text-slate-800" x-text="departmentsData.find(d => d.id == selectedDepartment)?.nama_dinas || '-'"></span>
                        </div>

                        <div class="flex justify-between gap-4 border-b border-slate-200 pb-3">
                            <span class="text-slate-500">Layanan Digital</span>
                            <span class="text-right font-bold text-slate-800" x-text="activeService.name || '-'"></span>
                        </div>

                        <div class="flex justify-between gap-4 border-b border-slate-200 pb-3">
                            <span class="text-slate-500">Judul / Subjek</span>
                            <span class="text-right font-bold text-slate-800" x-text="ticketTitle || 'Belum diisi'"></span>
                        </div>

                        <div>
                            <span class="mb-2 block text-slate-500">Deskripsi / Keterangan Lengkap</span>
                            <div class="whitespace-pre-line rounded-xl border border-slate-200 bg-white p-4 font-medium leading-relaxed text-slate-700" x-text="description || 'Belum ada deskripsi'"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button
                            type="button"
                            @click="currentStep = 2"
                            class="rounded-xl bg-slate-100 px-6 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-200"
                        >
                            Ubah Data
                        </button>
                        <button
                            type="submit"
                            class="flex items-center gap-2 rounded-xl bg-[#F59E0B] px-6 py-3 text-xs font-bold text-slate-950 shadow-sm transition hover:bg-[#D97706]"
                        >
                            <i class="fa-solid fa-paper-plane"></i>
                            Kirim Tiket Laporan
                        </button>
                    </div>
                </section>
            </div>
        </form>
    </main>
</x-app-layout>