<x-app-layout>
    <x-slot name="title">SLA & SOP - SLA Helpdesk</x-slot>
    <x-slot name="headerTitle">SLA & SOP</x-slot>

    <div
        x-data="{ search: '' }"
        class="max-w-6xl mx-auto space-y-6"
    >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-800">
                    Dokumen SLA & SOP
                </h1>

                <p class="text-xs text-slate-500 mt-1">
                    Informasi standar layanan dan prosedur operasional.
                </p>
            </div>

            <div class="relative w-full sm:w-72">
                <input
                    type="search"
                    x-model="search"
                    placeholder="Cari layanan atau OPD..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                >

                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($services ?? [] as $service)
                @php
                    $searchText = strtolower(
                        $service->name . ' ' .
                        $service->service_code . ' ' .
                        ($service->department->name ?? '')
                    );
                @endphp

                <article
                    x-show="'{{ $searchText }}'.includes(search.toLowerCase())"
                    class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition"
                >
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">

                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 bg-emerald-50 border border-emerald-100 rounded-xl shrink-0 flex items-center justify-center text-emerald-600 overflow-hidden">
                                @if($service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-file-lines text-2xl"></i>
                                @endif
                            </div>

                            <div>
                                <span class="text-xs font-bold text-emerald-700">
                                    {{ $service->service_code }}
                                </span>

                                <h2 class="text-base font-extrabold text-slate-900 mt-1">
                                    {{ $service->name }}
                                </h2>

                                <p class="text-xs text-slate-500 mt-1">
                                    <i class="fa-solid fa-building mr-1"></i>
                                    {{ $service->department->name ?? 'OPD belum ditentukan' }}
                                </p>

                                @if($service->response_time || $service->resolution_time)
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-bold">
                                            Respons {{ $service->response_time ?? '30 menit' }}
                                        </span>

                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-[10px] font-bold">
                                            Penyelesaian {{ $service->resolution_time ?? '8 jam' }}
                                        </span>

                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-bold">
                                            Uptime {{ $service->current_uptime ?? '100' }}%
                                        </span>
                                    </div>

                                    @if($service->valid_from && $service->valid_until)
                                        <p class="text-[11px] text-slate-400 mt-2">
                                            Berlaku: {{ \Carbon\Carbon::parse($service->valid_from)->format('d M Y') }} 
                                            · Reviu: {{ \Carbon\Carbon::parse($service->valid_until)->format('d M Y') }}
                                        </p>
                                    @endif
                                @else
                                    <p class="text-xs text-amber-600 font-semibold mt-3">
                                        Konfigurasi SLA belum tersedia.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if($service->sop_file)
                                <a
                                    href="{{ asset('storage/' . $service->sop_file) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#052e23] hover:bg-emerald-900 text-amber-400 rounded-xl text-xs font-bold transition shadow-sm"
                                >
                                    <i class="fa-solid fa-file-pdf"></i>
                                    Unduh SOP
                                </a>
                            @else
                                <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-400 rounded-xl text-xs font-bold border border-slate-200">
                                    <i class="fa-solid fa-file-circle-xmark"></i>
                                    SOP Belum Ada
                                </span>
                            @endif
                        </div>

                    </div>
                </article>
            @empty
                <div class="bg-white border border-slate-200 rounded-xl p-10 text-center">
                    <i class="fa-solid fa-folder-open text-3xl text-slate-300 mb-3"></i>

                    <p class="text-sm font-semibold text-slate-500">
                        Belum ada data layanan SLA.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>