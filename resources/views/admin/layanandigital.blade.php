<x-admin-layout>
    <x-slot name="title">SLA Monitoring - Layanan Digital</x-slot>
    <x-slot name="headerTitle">Layanan Digital</x-slot>

    <!-- LOAD JAVASCRIPT ASSETS VIA SLOT SCRIPTS -->
    <x-slot name="scripts">
        @vite(['resources/js/admin/layanandigital.js'])
        <script>
            window.storeRoute = "{{ route('admin.layanandigital.store') }}";
            @if(isset($services) && count($services) > 0)
                window.firstServiceData = {
                    id: "{{ $services[0]->id }}",
                    name: "{{ addslashes($services[0]->name) }}",
                    description: "{{ addslashes($services[0]->description ?? 'Sistem Layanan Digital') }}",
                    department: { name: "{{ addslashes($services[0]->department->name ?? '-') }}" },
                    type: "{{ $services[0]->type ?? 'pelayanan_publik' }}",
                    kepatuhan_sla: "{{ $services[0]->kepatuhan_sla ?? 100 }}",
                    image: "{{ $services[0]->image ?? '' }}",
                    dasar_hukum: "{{ addslashes($services[0]->dasar_hukum ?? '') }}",
                    persyaratan: "{{ addslashes($services[0]->persyaratan ?? '') }}",
                    sistem_prosedur: "{{ addslashes($services[0]->sistem_prosedur ?? '') }}",
                    jangka_waktu: "{{ addslashes($services[0]->jangka_waktu ?? '') }}",
                    biaya: "{{ addslashes($services[0]->biaya ?? 'Gratis') }}",
                    produk_pelayanan: "{{ addslashes($services[0]->produk_pelayanan ?? '') }}",
                    sarana_prasarana: "{{ addslashes($services[0]->sarana_prasarana ?? '') }}",
                    kompetensi_pelaksana: "{{ addslashes($services[0]->kompetensi_pelaksana ?? '') }}",
                    pengawasan_internal: "{{ addslashes($services[0]->pengawasan_internal ?? '') }}",
                    penanganan_pengaduan: "{{ addslashes($services[0]->penanganan_pengaduan ?? '') }}",
                    jumlah_pelaksana: "{{ addslashes($services[0]->jumlah_pelaksana ?? '') }}",
                    jaminan_pelayanan: "{{ addslashes($services[0]->jaminan_pelayanan ?? '') }}",
                    jaminan_keamanan: "{{ addslashes($services[0]->jaminan_keamanan ?? '') }}",
                    evaluasi_kinerja: "{{ addslashes($services[0]->evaluasi_kinerja ?? '') }}"
                };
            @endif

            // Fungsi Cetak / Download PDF Tabel Standar Pelayanan
            function cetakDokumen(data) {
                const item = typeof data === 'string' ? JSON.parse(data) : data;
                const win = window.open('', '_blank');
                
                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Standar Pelayanan - ${item.name || 'Layanan Digital'}</title>
                        <style>
                            body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; margin: 30px; color: #000; }
                            h2 { font-size: 14pt; font-weight: bold; margin-bottom: 15px; }
                            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                            th, td { border: 1px solid #000; padding: 8px 10px; vertical-align: top; text-align: left; font-size: 11pt; line-height: 1.4; }
                            th { font-weight: bold; text-align: center; background-color: #f2f2f2; }
                            .col-no { width: 5%; text-align: center; }
                            .col-komponen { width: 30%; font-weight: bold; }
                            .col-uraian { width: 65%; white-space: pre-line; }
                            @page { size: A4; margin: 20mm; }
                        </style>
                    </head>
                    <body>
                        <h2>2. &nbsp; ${item.name || 'Layanan Digital'}</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th class="col-no">NO</th>
                                    <th class="col-komponen">KOMPONEN</th>
                                    <th class="col-uraian">URAIAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-no">1.</td>
                                    <td class="col-komponen">Dasar Hukum</td>
                                    <td class="col-uraian">${item.dasar_hukum || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">2.</td>
                                    <td class="col-komponen">Persyaratan Pelayanan</td>
                                    <td class="col-uraian">${item.persyaratan || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">3.</td>
                                    <td class="col-komponen">Sistem, Mekanisme, dan Prosedur</td>
                                    <td class="col-uraian">${item.sistem_prosedur || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">4.</td>
                                    <td class="col-komponen">Jangka Waktu Penyelesaian</td>
                                    <td class="col-uraian">${item.jangka_waktu || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">5.</td>
                                    <td class="col-komponen">Biaya/Tarif</td>
                                    <td class="col-uraian">${item.biaya || 'Gratis'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">6.</td>
                                    <td class="col-komponen">Produk Pelayanan</td>
                                    <td class="col-uraian">${item.produk_pelayanan || item.name || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">7.</td>
                                    <td class="col-komponen">Sarana dan Prasarana dan/atau Fasilitas</td>
                                    <td class="col-uraian">${item.sarana_prasarana || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">8.</td>
                                    <td class="col-komponen">Kompetensi Pelaksana</td>
                                    <td class="col-uraian">${item.kompetensi_pelaksana || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">9.</td>
                                    <td class="col-komponen">Pengawasan Internal</td>
                                    <td class="col-uraian">${item.pengawasan_internal || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">10.</td>
                                    <td class="col-komponen">Penanganan, Pengaduan, Saran dan Masukan</td>
                                    <td class="col-uraian">${item.penanganan_pengaduan || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">11.</td>
                                    <td class="col-komponen">Jumlah Pelaksana</td>
                                    <td class="col-uraian">${item.jumlah_pelaksana || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">12.</td>
                                    <td class="col-komponen">Jaminan Pelayanan</td>
                                    <td class="col-uraian">${item.jaminan_pelayanan || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">13.</td>
                                    <td class="col-komponen">Jaminan Keamanan dan Keselamatan Pelayanan</td>
                                    <td class="col-uraian">${item.jaminan_keamanan || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="col-no">14.</td>
                                    <td class="col-komponen">Evaluasi Kinerja</td>
                                    <td class="col-uraian">${item.evaluasi_kinerja || '-'}</td>
                                </tr>
                            </tbody>
                        </table>
                        <script>
                            window.onload = function() { window.print(); }
                        <\/script>
                    </body>
                    </html>
                `;
                win.document.write(htmlContent);
                win.document.close();
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
                        <h3 class="text-2xl font-bold text-slate-800">{{ $totalLayanan ?? count($services ?? []) }}</h3>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-regular fa-circle-check"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Layanan Online</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ $layananAktif ?? count($services ?? []) }}</h3>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-regular fa-clock"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Maintenance</p>
                        <h3 class="text-2xl font-bold text-slate-800">0</h3>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Gangguan</p>
                        <h3 class="text-2xl font-bold text-slate-800">0</h3>
                    </div>
                </div>
            </div>

            <!-- TABLE SECTION -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between py-2">
                    <div class="flex items-center gap-3">
                        <select class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-700 bg-white"><option>Semua Kategori</option></select>
                    </div>
                    <button onclick="openModalTambah()" class="bg-[#052e23] hover:bg-emerald-900 text-amber-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition shadow-sm">
                        <i class="fa-solid fa-plus"></i><span>Tambah Layanan</span>
                    </button>
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
                                <tr class="hover:bg-slate-50 transition">
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
                                    <td class="py-4 px-4 font-medium">{{ $item->department->name ?? '-' }}</td>
                                    <td class="py-4 px-4 font-medium">{{ ucwords(str_replace('_', ' ', $item->type)) }}</td>
                                    <td class="py-4 px-4"><span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-semibold text-[10px]">Online</span></td>
                                    <td class="py-4 px-4 font-semibold text-slate-800">{{ $item->kepatuhan_sla ?? 100 }}%</td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="cetakDokumen({{ json_encode($item) }})" class="text-emerald-700 hover:text-emerald-900 transition" title="Unduh/Cetak Dokumen SLA">
                                                <i class="fa-solid fa-file-arrow-down text-sm"></i>
                                            </button>
                                            <button onclick="showDetail({{ json_encode($item) }})" class="text-slate-500 hover:text-[#052e23] transition" title="Lihat Detail">
                                                <i class="fa-regular fa-eye text-sm"></i>
                                            </button>
                                            <button onclick="openModalEdit({{ json_encode($item) }})" class="text-amber-600 hover:text-amber-700 transition" title="Edit Layanan">
                                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                                            </button>
                                            <form action="{{ route('admin.layanandigital.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                                                @csrf
                                                @method('DELETE')
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
                    <h4 id="detailTitle" class="font-bold text-sm text-slate-900">E-Planning KBB</h4>
                    <p id="detailDesc" class="text-xs text-slate-500 mt-1 leading-relaxed">Sistem Perencanaan Pembangunan Daerah</p>
                </div>
                <div class="text-xs space-y-0.5">
                    <p class="text-slate-400 font-medium">Perangkat Daerah</p>
                    <p id="detailDept" class="font-bold text-slate-800">Bappelitbangda</p>
                </div>
                <div class="text-xs space-y-0.5">
                    <p class="text-slate-400 font-medium">Jenis Layanan</p>
                    <p id="detailType" class="font-bold text-slate-800">Pelayanan Publik</p>
                </div>
                <div class="text-xs space-y-2 pt-3 border-t border-slate-100">
                    <h5 class="font-bold text-slate-800">Status Layanan</h5>
                    <div class="flex justify-between text-slate-600">
                        <span>Uptime (30 hari)</span><span id="detailUptime" class="font-bold text-slate-800">100%</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Status saat ini</span><span class="font-bold text-emerald-600">Online</span>
                    </div>
                </div>
                <div class="flex flex-col gap-2 pt-2">
                    <button id="btnUnduhDokumenDetail" onclick="cetakDokumen(window.firstServiceData)" class="w-full py-2.5 bg-emerald-700 text-white rounded-xl text-xs font-bold text-center hover:bg-emerald-800 transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download"></i><span>Unduh Dokumen SLA</span>
                    </button>
                    <a href="{{ route('admin.ticketing') }}" class="w-full py-2.5 bg-[#052e23] text-amber-400 rounded-xl text-xs font-bold text-center hover:bg-emerald-900 transition shadow-sm block">
                        Buat Tiket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL POPUP FORM (14 KOMPONEN STANDAR PELAYANAN) -->
    <div id="modalFormLayanan" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[92vh] flex flex-col">
            <!-- Header Modal -->
            <div class="bg-[#052e23] px-6 py-4 flex justify-between items-center text-white shrink-0">
                <h3 id="modalTitle" class="font-bold text-base">Tambah Layanan Digital</h3>
                <button onclick="closeModalForm()" class="text-white text-xl font-bold hover:text-amber-400">&times;</button>
            </div>
            
            <!-- Body Form Scrollable -->
            <form id="formLayanan" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-4 text-xs overflow-y-auto flex-1">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="border-b border-slate-200 pb-3 font-bold text-slate-800 text-sm">
                    A. INFORMASI UMUM LAYANAN
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Layanan Digital</label>
                        <input type="text" name="name" id="inputNama" required placeholder="Contoh: Layanan Hosting" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Layanan / Akronim</label>
                        <input type="text" name="service_code" id="inputKode" required placeholder="Contoh: HOSTING" class="w-full border border-slate-300 rounded-lg p-2.5 uppercase focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Perangkat Daerah</label>
                        <input type="text" name="department_name" id="inputDepartment" required placeholder="Contoh: Diskominfotik" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jenis Layanan</label>
                        <select name="type" id="inputType" required class="w-full border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                            <option value="pelayanan_publik">Pelayanan Publik</option>
                            <option value="administrasi_pemerintahan">Administrasi Pemerintahan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Logo / Gambar Layanan</label>
                    <input type="file" name="image" id="inputImage" accept="image/*" class="w-full border border-slate-300 rounded-lg p-2 text-slate-500 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div class="border-b border-slate-200 pt-4 pb-2 font-bold text-slate-800 text-sm">
                    B. 14 KOMPONEN STANDAR PELAYANAN (SLA)
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">1. DASAR HUKUM</label>
                    <textarea name="dasar_hukum" id="inputDasarHukum" rows="3" placeholder="Contoh: Peraturan Presiden Nomor 95 Tahun 2018..." class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">2. PERSYARATAN PELAYANAN</label>
                    <textarea name="persyaratan" id="inputPersyaratan" rows="2" placeholder="Contoh: Surat Permohonan hosting aplikasi..." class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">3. SISTEM MEKANISME DAN PROSEDUR</label>
                    <textarea name="sistem_prosedur" id="inputSistemProsedur" rows="4" placeholder="1. Mengajukan surat permohonan...&#10;2. Memverifikasi dan mendisposisikan..." class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">4. JANGKA WAKTU PENYELESAIAN</label>
                        <input type="text" name="jangka_waktu" id="inputJangkaWaktu" placeholder="Contoh: 1 Jam" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">5. BIAYA ATAU TARIF</label>
                        <input type="text" name="biaya" id="inputBiaya" placeholder="Contoh: Gratis" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">6. PRODUK PELAYANAN</label>
                    <input type="text" name="produk_pelayanan" id="inputProdukPelayanan" placeholder="Contoh: Layanan Hosting" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">7. SARANA DAN PRASARANA DAN ATAU FASILITAS</label>
                    <textarea name="sarana_prasarana" id="inputSaranaPrasarana" rows="2" placeholder="1. Surat Permohonan&#10;2. Server&#10;3. Aplikasi" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">8. KOMPETENSI PELAKSANA</label>
                    <textarea name="kompetensi_pelaksana" id="inputKompetensiPelaksana" rows="2" placeholder="Contoh: Tenaga Ahli Jaringan dan Staff Pelaksana Jaringan" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">9. PENGAWASAN INTERNAL</label>
                    <input type="text" name="pengawasan_internal" id="inputPengawasanInternal" placeholder="Contoh: Kepala Bidang Aplikasi dan Informatika Pemerintahan" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">10. PENANGANAN, PENGADUAN, SARAN, DAN MASUKAN</label>
                    <input type="text" name="penanganan_pengaduan" id="inputPenangananPengaduan" placeholder="Contoh: PPID, SP4N LAPOR, dll" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">11. JUMLAH PELAKSANA</label>
                        <input type="text" name="jumlah_pelaksana" id="inputJumlahPelaksana" placeholder="Contoh: 8 Orang" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">13. JAMINAN KEAMANAN DAN KESELAMATAN</label>
                        <input type="text" name="jaminan_keamanan" id="inputJaminanKeamanan" placeholder="Contoh: Tersedia" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">12. JAMINAN PELAYANAN</label>
                    <textarea name="jaminan_pelayanan" id="inputJaminanPelayanan" rows="2" placeholder="Contoh: Tersimpannya semua file, data, gambar..." class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">14. EVALUASI KINERJA</label>
                    <input type="text" name="evaluasi_kinerja" id="inputEvaluasiKinerja" placeholder="Contoh: Laporan secara berkala kepada pimpinan" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-[#052e23] focus:outline-none">
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end gap-2 pt-4 border-t border-slate-200">
                    <button type="button" onclick="closeModalForm()" class="px-4 py-2 border border-slate-300 rounded-lg font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#052e23] text-amber-400 font-bold rounded-lg hover:bg-emerald-900 transition">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>