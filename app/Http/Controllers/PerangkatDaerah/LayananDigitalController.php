<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LayananDigitalController extends Controller
{
    public function index()
    {
        $services = Service::with(['tickets', 'department'])
            ->latest()
            ->get();

        $maxHours = 8;
        $now = Carbon::now();

        $services->transform(function ($service) use ($maxHours, $now) {
            $totalTickets = $service->tickets->count();

            if ($totalTickets > 0) {
                $overdueTickets = $service->tickets
                    ->filter(function ($ticket) use ($maxHours, $now) {
                        $isDone = in_array(
                            strtoupper($ticket->status),
                            ['RESOLVED', 'COMPLETED', 'CLOSED']
                        );

                        if ($isDone) {
                            return false;
                        }

                        $deadline = Carbon::parse($ticket->created_at)
                            ->addHours($maxHours);

                        return $now->greaterThan($deadline);
                    })
                    ->count();

                $kepatuhan = round(
                    (($totalTickets - $overdueTickets) / $totalTickets) * 100
                );
            } else {
                $kepatuhan = 100;
            }

            $service->total_tickets = $totalTickets;
            $service->kepatuhan_sla = $kepatuhan;

            return $service;
        });

        $totalLayanan = $services->count();

        $layananAktif = $services
            ->where('status', 'active')
            ->count();

        $departments = Department::all();

        return view(
            'admin.layanandigital',
            compact(
                'services',
                'totalLayanan',
                'layananAktif',
                'departments'
            )
        );
    }

    /**
     * Membuat atau mengambil data perangkat daerah.
     */
    protected function resolveDepartment(
        string $departmentName
    ): Department {
        $name = trim($departmentName);

        $existingDepartment = Department::whereRaw(
            'LOWER(name) = ?',
            [strtolower($name)]
        )->first();

        if ($existingDepartment) {
            if (empty($existingDepartment->code)) {
                $existingDepartment->code = $this->generateDepartmentCode(
                    $name,
                    $existingDepartment->id
                );

                $existingDepartment->save();
            }

            return $existingDepartment;
        }

        return Department::create([
            'name' => $name,
            'code' => $this->generateDepartmentCode($name),
        ]);
    }

    /**
     * Membuat kode perangkat daerah yang unik.
     */
    protected function generateDepartmentCode(
        string $name,
        ?int $existingId = null
    ): string {
        $base = preg_replace(
            '/[^A-Za-z0-9]+/',
            '',
            strtoupper(Str::slug($name, ' '))
        );

        $base = substr($base ?: 'DEP', 0, 20);

        $candidate = $base;
        $suffix = 1;

        while (
            Department::where('code', $candidate)
                ->when($existingId, function ($query, $id) {
                    return $query->where('id', '!=', $id);
                })
                ->exists()
        ) {
            $candidate = substr(
                $base,
                0,
                20 - strlen((string) $suffix) - 1
            ) . $suffix;

            $suffix++;
        }

        return $candidate;
    }

    /**
     * Aturan validasi data layanan.
     */
    protected function validationRules(
        bool $isUpdate = false,
        ?int $id = null
    ): array {
        $serviceCodeRule = 'required|string|max:20|unique:services,service_code';

        if ($isUpdate) {
            $serviceCodeRule .= ',' . $id;
        }

        return [
            // Informasi umum
            'name' => 'required|string|max:100',
            'service_code' => $serviceCodeRule,
            'department_name' => 'required|string|max:255',
            'type' => 'required|in:pelayanan_publik,administrasi_pemerintahan',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'sop_file' => 'nullable|file|max:15360',

            // Informasi SLA yang sudah ada
            'response_time' => 'nullable|string|max:50',
            'resolution_time' => 'nullable|string|max:50',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date',

            // Bagian keputusan
            'menimbang' => 'nullable|string',
            'mengingat' => 'nullable|string',

            // B. Komponen Standar Pelayanan
            'dasar_hukum' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'sistem_prosedur' => 'nullable|string',
            'jangka_waktu' => 'nullable|string|max:255',
            'biaya' => 'nullable|string|max:255',
            'produk_pelayanan' => 'nullable|string',
            'sarana_prasarana' => 'nullable|string',
            'kompetensi_pelaksana' => 'nullable|string',
            'pengawasan_internal' => 'nullable|string',
            'penanganan_pengaduan' => 'nullable|string',
            'jumlah_pelaksana' => 'nullable|string|max:255',
            'jaminan_pelayanan' => 'nullable|string',
            'jaminan_keamanan' => 'nullable|string',
            'evaluasi_kinerja' => 'nullable|string',
        ];
    }

    /**
     * Mengambil data yang akan disimpan dari form.
     */
    protected function serviceData(
        Request $request,
        Department $department,
        ?string $imagePath,
        ?string $sopPath,
        bool $isUpdate = false
    ): array {
        $data = [
            // Informasi umum
            'name' => $request->name,
            'service_code' => strtoupper($request->service_code),
            'department_id' => $department->id,
            'type' => $request->type,
            'description' => $request->input('description'),
            'image' => $imagePath,
            'sop_file' => $sopPath,

            // Informasi SLA yang sudah ada
            'response_time' => $isUpdate
                ? $request->input('response_time')
                : ($request->input('response_time') ?? '30 menit'),

            'resolution_time' => $isUpdate
                ? $request->input('resolution_time')
                : ($request->input('resolution_time') ?? '1 jam'),

            'valid_from' => $request->input('valid_from'),
            'valid_until' => $request->input('valid_until'),

            // Menimbang dan Mengingat
            'menimbang' => $request->input('menimbang'),
            'mengingat' => $request->input('mengingat'),

            // B. Standar Pelayanan
            'dasar_hukum' => $request->input('dasar_hukum'),
            'persyaratan' => $request->input('persyaratan'),
            'sistem_prosedur' => $request->input('sistem_prosedur'),
            'jangka_waktu' => $request->input('jangka_waktu'),
            'biaya' => $request->input('biaya') ?: 'Gratis',
            'produk_pelayanan' => $request->input('produk_pelayanan'),
            'sarana_prasarana' => $request->input('sarana_prasarana'),
            'kompetensi_pelaksana' => $request->input('kompetensi_pelaksana'),
            'pengawasan_internal' => $request->input('pengawasan_internal'),
            'penanganan_pengaduan' => $request->input('penanganan_pengaduan'),
            'jumlah_pelaksana' => $request->input('jumlah_pelaksana'),
            'jaminan_pelayanan' => $request->input('jaminan_pelayanan'),
            'jaminan_keamanan' => $request->input('jaminan_keamanan'),
            'evaluasi_kinerja' => $request->input('evaluasi_kinerja'),
        ];

        if (!$isUpdate) {
            $data['status'] = 'active';
            $data['current_uptime'] = 100.00;
        }

        return $data;
    }

    /**
     * Menambahkan layanan digital.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->validationRules()
        );

        $department = $this->resolveDepartment(
            $validated['department_name']
        );

        Storage::disk('public')->makeDirectory('services');
        Storage::disk('public')->makeDirectory('sop_files');

        $imagePath = null;
        $sopPath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('services', 'public');
        }

        if ($request->hasFile('sop_file')) {
            $sopPath = $request->file('sop_file')
                ->store('sop_files', 'public');
        }

        Service::create(
            $this->serviceData(
                $request,
                $department,
                $imagePath,
                $sopPath
            )
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Layanan digital dan dokumen SOP berhasil ditambahkan!'
            );
    }

    /**
     * Memperbarui layanan digital.
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate(
            $this->validationRules(true, (int) $id)
        );

        $department = $this->resolveDepartment(
            $validated['department_name']
        );

        Storage::disk('public')->makeDirectory('services');
        Storage::disk('public')->makeDirectory('sop_files');

        // Pertahankan gambar lama jika tidak ada gambar baru.
        $imagePath = $service->image;

        if ($request->hasFile('image')) {
            if (
                $service->image &&
                Storage::disk('public')->exists($service->image)
            ) {
                Storage::disk('public')->delete($service->image);
            }

            $imagePath = $request->file('image')
                ->store('services', 'public');
        }

        // Pertahankan SOP lama jika tidak ada file baru.
        $sopPath = $service->sop_file;

        if ($request->hasFile('sop_file')) {
            if (
                $service->sop_file &&
                Storage::disk('public')->exists($service->sop_file)
            ) {
                Storage::disk('public')->delete($service->sop_file);
            }

            $sopPath = $request->file('sop_file')
                ->store('sop_files', 'public');
        }

        $service->update(
            $this->serviceData(
                $request,
                $department,
                $imagePath,
                $sopPath,
                true
            )
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Data layanan digital dan dokumen SOP berhasil diperbarui!'
            );
    }

    /**
     * Menghapus layanan digital.
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        if (
            $service->image &&
            Storage::disk('public')->exists($service->image)
        ) {
            Storage::disk('public')->delete($service->image);
        }

        if (
            $service->sop_file &&
            Storage::disk('public')->exists($service->sop_file)
        ) {
            Storage::disk('public')->delete($service->sop_file);
        }

        $service->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Data layanan digital berhasil dihapus!'
            );
    }
}