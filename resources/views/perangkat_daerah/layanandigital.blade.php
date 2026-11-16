<x-perangkat-daerah-layout>
    <x-slot name="title">SLA Monitoring - Layanan Digital Perangkat Daerah</x-slot>
    <x-slot name="headerTitle">Layanan Digital</x-slot>

    @php($firstService = $services->first())

    <!-- LOAD SKRIP LOKAL -->
    <x-slot name="scripts">
        <script>
            // Variabel global untuk menyimpan data layanan yang sedang aktif/dilihat di detail
            window.currentDetailItem = @json($firstService);
            window.documentDownloadRouteTemplate = @json(route('perangkat-daerah.layanandigital.document', ['service' => '__ID__']));

            // Fungsi Membuka Modal Tambah
            function openModalTambah() {
                document.getElementById('modalTitle').innerText = 'Tambah Layanan Digital';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('formLayanan').action = "{{ route('perangkat-daerah.layanandigital.store') }}";
                document.getElementById('formLayanan').reset();
                document.getElementById('inputImage').value = '';
                document.getElementById('modalFormLayanan').classList.remove('hidden');
                document.getElementById('modalFormLayanan').classList.add('flex');
            }

            // Fungsi Membuka Modal Edit
            function openModalEdit(item) {
                document.getElementById('modalTitle').innerText = 'Edit Layanan Digital';
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('formLayanan').action = `/perangkat-daerah/layanandigital/${item.id}`;
                
                document.getElementById('inputNama').value = item.name || '';
                document.getElementById('inputKode').value = item.service_code || '';
                document.getElementById('inputType').value = item.type || 'pelayanan_publik';
                document.getElementById('inputStatus').value = item.status || 'active';
                document.getElementById('inputImage').value = '';

                document.getElementById('inputMenimbang').value = item.menimbang || '';
                document.getElementById('inputMengingat').value = item.mengingat || '';
                document.getElementById('inputDasarHukum').value = item.dasar_hukum || '';
                document.getElementById('inputPersyaratan').value = item.persyaratan || '';
                document.getElementById('inputSistemProsedur').value = item.sistem_prosedur || '';
                document.getElementById('inputJangkaWaktu').value = item.jangka_waktu || '';
                document.getElementById('inputBiaya').value = item.biaya || '';
                document.getElementById('inputProdukPelayanan').value = item.produk_pelayanan || '';
                document.getElementById('inputSaranaPrasarana').value = item.sarana_prasarana || '';
                document.getElementById('inputKompetensiPelaksana').value = item.kompetensi_pelaksana || '';
                document.getElementById('inputPengawasanInternal').value = item.pengawasan_internal || '';
                document.getElementById('inputPenangananPengaduan').value = item.penanganan_pengaduan || '';
                document.getElementById('inputJumlahPelaksana').value = item.jumlah_pelaksana || '';
                document.getElementById('inputJaminanKeamanan').value = item.jaminan_keamanan || '';
                document.getElementById('inputJaminanPelayanan').value = item.jaminan_pelayanan || '';
                document.getElementById('inputEvaluasiKinerja').value = item.evaluasi_kinerja || '';

                document.getElementById('modalFormLayanan').classList.remove('hidden');
                document.getElementById('modalFormLayanan').classList.add('flex');
            }

            // Fungsi Menutup Modal
            function closeModalForm() {
                document.getElementById('modalFormLayanan').classList.remove('flex');
                document.getElementById('modalFormLayanan').classList.add('hidden');
            }

            // Fungsi Detail Panel Samping
            function showDetail(item) {
                window.currentDetailItem = item;

                document.getElementById('detailTitle').innerText = item.name || '-';
                document.getElementById('detailDesc').innerText = item.description || 'Sistem Layanan Digital';
                document.getElementById('detailDept').innerText = item.department_name || '-';
                document.getElementById('detailType').innerText = item.type ? item.type.replace('_', ' ').toUpperCase() : '-';
                document.getElementById('detailUptime').innerText = (item.current_uptime || 100) + '%';
                
                const statusSpan = document.getElementById('detailStatusText');
                if (statusSpan) {
                    if (item.status === 'active') {
                        statusSpan.innerText = 'Online';
                        statusSpan.className = 'font-bold text-emerald-600';
                    } else if (item.status === 'maintenance') {
                        statusSpan.innerText = 'Maintenance';
                        statusSpan.className = 'font-bold text-amber-600';
                    } else {
                        statusSpan.innerText = 'Gangguan';
                        statusSpan.className = 'font-bold text-rose-600';
                    }
                }

                const imgTag = document.getElementById('detailImageTag');
                const imgIcon = document.getElementById('detailImageIcon');
                if (item.image) {
                    imgTag.src = `/storage/${item.image}`;
                    imgTag.classList.remove('hidden');
                    imgIcon.classList.add('hidden');
                } else {
                    imgTag.classList.add('hidden');
                    imgIcon.classList.remove('hidden');
                }
            }

            // Unduh template standar pelayanan yang sudah diisi dari data tersimpan.
            function cetakDokumen(data) {
                if (!data) {
                    alert('Data layanan tidak ditemukan.');
                    return;
                }
                const item = typeof data === 'string' ? JSON.parse(data) : data;
                if (!item.id || !window.documentDownloadRouteTemplate) {
                    alert('Dokumen layanan tidak dapat diunduh. Muat ulang halaman lalu coba kembali.');
                    return;
                }
                window.location.href = window.documentDownloadRouteTemplate.replace('__ID__', encodeURIComponent(item.id));
            }
        </script>
    </x-slot>

    <div class="p-6 flex flex-col xl:flex-row gap-6">
        <div class="flex-1 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between">
                    <span><i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}</span>
                </div>
            @endif

            <!-- Metric Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-emerald-50 text-[#052e23] rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-border-all"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Total Layanan</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ count($services ?? []) }}</h3>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-regular fa-circle-check"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Layanan Online</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ collect($services ?? [])->where('status', 'active')->count() }}</h3>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-regular fa-clock"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Maintenance</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ collect($services ?? [])->where('status', 'maintenance')->count() }}</h3>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Gangguan</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ collect($services ?? [])->where('status', 'gangguan')->count() }}</h3>
                    </div>
                </div>
            </div>

            <!-- TABLE SECTION -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between py-2">
                    <div class="flex items-center gap-3">
                        <select class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-700 bg-white"><option>Semua Kategori</option></select>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('perangkat-daerah.layanandigital.document-all') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition shadow-sm">
                            <i class="fa-solid fa-file-arrow-down"></i><span>Unduh Semua SLA</span>
                        </a>
                        <button onclick="openModalTambah()" class="bg-[#052e23] hover:bg-emerald-900 text-amber-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition shadow-sm">
                            <i class="fa-solid fa-plus"></i><span>Tambah Layanan</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 bg-slate-50">
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-4">Layanan Digital</th>
                                <th class="py-3 px-4">Perangkat Daerah</th>
                                <th class="py-3 px-4">Jenis</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Kepatuhan SLA</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($services ?? [] as $index => $item)
                                <tr class="hover:bg-slate-50 transition" onclick="showDetail({{ json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) }})">
                                    <td class="py-4 px-3 text-center font-medium">{{ $index + 1 }}</td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 border border-slate-200 rounded-lg flex items-center justify-center bg-emerald-50 text-[#052e23] shrink-0 font-bold text-xs overflow-hidden">
                                                @if($item->image)
                                                    <img src="{{ asset('storage/' . $item->image) }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    {{ strtoupper(substr($item->name ?? 'APP', 0, 2)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm">{{ $item->name }}</div>
                                                <div class="text-[11px] text-slate-500">{{ $item->description ?? 'Layanan Digital' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-medium">{{ $item->department_name ?? '-' }}</td>
                                    <td class="py-4 px-4 font-medium">{{ ucwords(str_replace('_', ' ', $item->type)) }}</td>
                                    <td class="py-4 px-4">
                                        @if($item->status == 'active')
                                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-semibold text-[10px]">Online</span>
                                        @elseif($item->status == 'maintenance')
                                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-semibold text-[10px]">Maintenance</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full font-semibold text-[10px]">Gangguan</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 font-semibold text-slate-800">{{ $item->kepatuhan_sla ?? 100 }}%</td>
                                    <td class="py-4 px-4 text-center" onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="cetakDokumen({{ json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) }})" class="text-emerald-700 hover:text-emerald-900 transition" title="Unduh/Cetak Dokumen SLA">
                                                <i class="fa-solid fa-file-arrow-down text-sm"></i>
                                            </button>
                                            <button onclick="showDetail({{ json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) }})" class="text-slate-500 hover:text-[#052e23] transition" title="Lihat Detail">
                                                <i class="fa-regular fa-eye text-sm"></i>
                                            </button>
                                            <button onclick="openModalEdit({{ json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) }})" class="text-amber-600 hover:text-amber-700 transition" title="Edit Layanan">
                                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                                            </button>
                                            <form action="{{ route('perangkat-daerah.layanandigital.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-rose-500 hover:text-rose-700 transition" title="Hapus Layanan">
                                                    <i class="fa-solid fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-8 text-center text-slate-400">Belum ada data layanan digital terdaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: DETAIL LAYANAN -->
        <div class="w-full xl:w-80 shrink-0 space-y-4">
            <div class="bg-white rounded-2xl p-5 space-y-4 border border-slate-200 shadow-sm sticky top-24">
                <h3 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-3">Detail Layanan</h3>
                <div id="detailImageContainer" class="w-full h-36 bg-slate-100 border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 overflow-hidden">
                    <i class="fa-regular fa-image text-4xl" id="detailImageIcon"></i>
                    <img id="detailImageTag" src="" alt="" class="w-full h-full object-cover hidden">
                </div>
                <div>
                    <h4 id="detailTitle" class="font-bold text-sm text-slate-900">{{ $firstService?->name ?? 'Pilih Layanan' }}</h4>
                    <p id="detailDesc" class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $firstService?->description ?? 'Sistem Layanan Digital' }}</p>
                </div>
                <div class="text-xs space-y-0.5">
                    <p class="text-slate-400 font-medium">Perangkat Daerah</p>
                    <p id="detailDept" class="font-bold text-slate-800">{{ $firstService?->department_name ?? '-' }}</p>
                </div>
                <div class="text-xs space-y-0.5">
                    <p class="text-slate-400 font-medium">Jenis Layanan</p>
                    <p id="detailType" class="font-bold text-slate-800">{{ ucwords(str_replace('_', ' ', $firstService?->type ?? '-')) }}</p>
                </div>
                <div class="text-xs space-y-2 pt-3 border-t border-slate-100">
                    <h5 class="font-bold text-slate-800">Status Layanan</h5>
                    <div class="flex justify-between text-slate-600">
                        <span>Uptime (30 hari)</span><span id="detailUptime" class="font-bold text-slate-800">100%</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Status saat ini</span><span id="detailStatusText" class="font-bold text-emerald-600">{{ $firstService ? ($firstService->status === 'active' ? 'Online' : 'Maintenance') : 'Belum tersedia' }}</span>
                    </div>
                </div>
                <div class="flex flex-col gap-2 pt-2">
                    <button onclick="cetakDokumen(window.currentDetailItem)" class="w-full py-2.5 bg-emerald-700 text-white rounded-xl text-xs font-bold text-center hover:bg-emerald-800 transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download"></i><span>Unduh Dokumen SLA</span>
                    </button>
                    <a href="{{ route('perangkat-daerah.ticketing') }}" class="w-full py-2.5 bg-[#052e23] text-amber-400 rounded-xl text-xs font-bold text-center hover:bg-emerald-900 transition shadow-sm block">
                        Buat Tiket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL POPUP FORM -->
    <div id="modalFormLayanan" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[92vh] flex flex-col">
            <div class="bg-[#052e23] px-6 py-4 flex justify-between items-center text-white shrink-0">
                <h3 id="modalTitle" class="font-bold text-base">Tambah Layanan Digital</h3>
                <button onclick="closeModalForm()" class="text-white text-xl font-bold hover:text-amber-400">&times;</button>
            </div>
            
            <form id="formLayanan" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-4 text-xs overflow-y-auto flex-1">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="response_time" value="30 menit">
                <input type="hidden" name="resolution_time" value="8 jam">

                <div class="border-b border-slate-200 pb-3 font-bold text-slate-800 text-sm">
                    A. INFORMASI UMUM LAYANAN
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Layanan Digital</label>
                        <input type="text" name="name" id="inputNama" required placeholder="Contoh: Layanan Hosting" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Layanan / Akronim</label>
                        <input type="text" name="service_code" id="inputKode" required placeholder="Contoh: HOSTING" class="w-full border border-slate-300 rounded-lg p-2.5 uppercase focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Perangkat Daerah</label>
                        <input type="text" 
                               name="department_name" 
                               id="inputDepartment" 
                               value="{{ \App\Models\DinasAdmin::where('email', Auth::user()->email)->value('nama_dinas') ?? Auth::user()->name }}" 
                               readonly 
                               class="w-full border border-slate-300 bg-slate-100 rounded-lg p-2.5 text-slate-700 cursor-not-allowed font-semibold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jenis Layanan</label>
                        <select name="type" id="inputType" required class="w-full border border-slate-300 rounded-lg p-2.5 bg-white focus:outline-none">
                            <option value="pelayanan_publik">Pelayanan Publik</option>
                            <option value="administrasi_pemerintahan">Administrasi Pemerintahan</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Status Layanan</label>
                        <select name="status" id="inputStatus" required class="w-full border border-slate-300 rounded-lg p-2.5 bg-white focus:outline-none">
                            <option value="active">Online</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="gangguan">Gangguan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Logo / Gambar Layanan <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="file" 
                           name="image" 
                           id="inputImage" 
                           accept="image/*" 
                           class="w-full border border-slate-300 rounded-lg p-2 text-slate-600 bg-white file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#052e23] hover:file:bg-emerald-100 cursor-pointer focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Menimbang</label>
                    <textarea name="menimbang" id="inputMenimbang" rows="3" placeholder="Masukkan satu poin per baris; huruf a., b., c., dan seterusnya dibuat otomatis." class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Mengingat</label>
                    <textarea name="mengingat" id="inputMengingat" rows="3" placeholder="Masukkan satu dasar hukum per baris; nomor 1., 2., 3., dan seterusnya dibuat otomatis." class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none"></textarea>
                </div>

                <div class="border-b border-slate-200 pt-4 pb-2 font-bold text-slate-800 text-sm">
                    B. 14 KOMPONEN STANDAR PELAYANAN (SLA)
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">1. DASAR HUKUM</label>
                    <textarea name="dasar_hukum" id="inputDasarHukum" rows="3" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">2. PERSYARATAN PELAYANAN</label>
                    <textarea name="persyaratan" id="inputPersyaratan" rows="2" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">3. SISTEM MEKANISME DAN PROSEDUR</label>
                    <textarea name="sistem_prosedur" id="inputSistemProsedur" rows="4" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">4. JANGKA WAKTU PENYELESAIAN</label>
                        <input type="text" name="jangka_waktu" id="inputJangkaWaktu" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">5. BIAYA ATAU TARIF</label>
                        <input type="text" name="biaya" id="inputBiaya" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">6. PRODUK PELAYANAN</label>
                    <input type="text" name="produk_pelayanan" id="inputProdukPelayanan" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">7. SARANA DAN PRASARANA</label>
                    <textarea name="sarana_prasarana" id="inputSaranaPrasarana" rows="2" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">8. KOMPETENSI PELAKSANA</label>
                    <textarea name="kompetensi_pelaksana" id="inputKompetensiPelaksana" rows="2" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">9. PENGAWASAN INTERNAL</label>
                    <input type="text" name="pengawasan_internal" id="inputPengawasanInternal" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">10. PENANGANAN PENGADUAN</label>
                    <input type="text" name="penanganan_pengaduan" id="inputPenangananPengaduan" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">11. JUMLAH PELAKSANA</label>
                        <input type="text" name="jumlah_pelaksana" id="inputJumlahPelaksana" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">13. JAMINAN KEAMANAN</label>
                        <input type="text" name="jaminan_keamanan" id="inputJaminanKeamanan" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">12. JAMINAN PELAYANAN</label>
                    <textarea name="jaminan_pelayanan" id="inputJaminanPelayanan" rows="2" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">14. EVALUASI KINERJA</label>
                    <input type="text" name="evaluasi_kinerja" id="inputEvaluasiKinerja" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none">
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end gap-2 pt-4 border-t border-slate-200">
                    <button type="button" onclick="closeModalForm()" class="px-4 py-2 border border-slate-300 rounded-lg font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#052e23] text-amber-400 font-bold rounded-lg hover:bg-emerald-900 transition">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</x-perangkat-daerah-layout>