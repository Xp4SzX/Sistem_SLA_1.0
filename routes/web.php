<?php

use Illuminate\Support\Facades\Route;

// Import Common / Auth Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SlaSopController;

// Import User / Pelapor Controller
use App\Http\Controllers\User\TicketController as PelaporTicketController;

// Import Admin Pusat Controllers (Super Admin KBB)
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HelpdeskController as AdminHelpdeskController;
use App\Http\Controllers\Admin\SlaMonitoringController as AdminSlaMonitoringController;
use App\Http\Controllers\Admin\LayananDigitalController as AdminLayananDigitalController;
use App\Http\Controllers\Admin\EvaluasiController as AdminEvaluasiController;
use App\Http\Controllers\Admin\ServiceDocumentController;
use App\Http\Controllers\DinasAdminController;

// Import Perangkat Daerah / Admin Dinas Controllers
use App\Http\Controllers\PerangkatDaerah\DashboardController as PerangkatDaerahDashboardController;
use App\Http\Controllers\PerangkatDaerah\TicketController as PerangkatDaerahTicketController;
use App\Http\Controllers\PerangkatDaerah\ServiceController as PerangkatDaerahServiceController;
use App\Http\Controllers\PerangkatDaerah\TechnicianController as PerangkatDaerahTechnicianController;
use App\Http\Controllers\PerangkatDaerah\EvaluasiController as PerangkatDaerahEvaluasiController;

/*
|--------------------------------------------------------------------------
| Public Routes (Halaman Umum)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cek-tiket', [PelaporTicketController::class, 'trackPublic'])->name('tickets.track-public');

/*
|--------------------------------------------------------------------------
| Guest Routes (Autentikasi)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Auth Pelapor / Pemohon
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'loginUser']);

    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'registerUser']);

    // Auth Admin & Perangkat Daerah
    Route::get('/admin/login', fn() => view('admin.auth.login'))->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
});

// Logout Bersama
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| PELAPOR / USER ROUTES (Portal Masyarakat)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PelaporTicketController::class, 'index'])->name('dashboard');

    // Kelola Tiket Pelapor
    Route::get('/ticketing', [PelaporTicketController::class, 'ticketingView'])->name('user.ticketing');
    Route::get('/tickets', [PelaporTicketController::class, 'ticketingView'])->name('tickets.index');
    
    Route::get('/buat-tiket', [PelaporTicketController::class, 'create'])->name('create-ticket');
    Route::get('/tickets/create', [PelaporTicketController::class, 'create'])->name('tickets.create');
    Route::post('/buat-tiket', [PelaporTicketController::class, 'store'])->name('tickets.store');

    Route::get('/tickets/{ticket_no}', [PelaporTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket_no}/rating', [PelaporTicketController::class, 'rate'])->name('tickets.rate');

    // Informasi SLA & Profile
    Route::get('/sla-sop', [SlaSopController::class, 'index'])->name('sla-sop');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN / DISKOMINFO ROUTES (Pusat Kontrol Super Admin KBB)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard Utama Admin Pusat
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Kelola Akun Admin Dinas (Oleh Admin Pusat)
    Route::get('/kelola-dinas', [DinasAdminController::class, 'index'])->name('dinas.index');
    Route::post('/kelola-dinas', [DinasAdminController::class, 'store'])->name('dinas.store');
    Route::put('/kelola-dinas/{id}', [DinasAdminController::class, 'update'])->name('dinas.update');
    Route::delete('/kelola-dinas/{id}', [DinasAdminController::class, 'destroy'])->name('dinas.destroy');

    // Kelola Helpdesk & Tiket Lintas Dinas
    Route::get('/ticketing', [AdminHelpdeskController::class, 'index'])->name('ticketing');
    Route::get('/tickets/{id}', [AdminHelpdeskController::class, 'show'])->name('tickets.show');
    Route::match(['post', 'put'], '/tickets/{id}/update-status', [AdminHelpdeskController::class, 'updateStatus'])->name('tickets.update-status');

    // Monitoring SLA Keseluruhan
    Route::get('/sla-monitoring', [AdminSlaMonitoringController::class, 'index'])->name('sla-monitoring');

    // Kelola Layanan Digital Pusat
    Route::get('/layanandigital', [AdminLayananDigitalController::class, 'index'])->name('layanandigital');
    Route::post('/layanandigital', [AdminLayananDigitalController::class, 'store'])->name('layanandigital.store');
    Route::put('/layanandigital/{id}', [AdminLayananDigitalController::class, 'update'])->name('layanandigital.update');
    Route::delete('/layanandigital/{id}', [AdminLayananDigitalController::class, 'destroy'])->name('layanandigital.destroy');

    // Evaluasi & Rekapitulasi Laporan
    Route::get('/evaluasi', [AdminEvaluasiController::class, 'index'])->name('evaluasi');
    Route::get('/evaluasi/export-pdf', [AdminEvaluasiController::class, 'exportPdf'])->name('evaluasi.pdf');
});

/*
|--------------------------------------------------------------------------
| PERANGKAT DAERAH / ADMIN DINAS ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('perangkat-daerah')->name('perangkat-daerah.')->middleware(['auth'])->group(function () {
    // 1. Dashboard Perangkat Daerah
    Route::get('/dashboard', [PerangkatDaerahDashboardController::class, 'index'])->name('dashboard');

    // 2. Ticketing Perangkat Daerah
    Route::get('/ticketing', [PerangkatDaerahTicketController::class, 'index'])->name('ticketing');
    Route::match(['post', 'put'], '/tickets/{id}/update-status', [PerangkatDaerahTicketController::class, 'updateStatus'])->name('tickets.update-status');

    // 3. Layanan Digital Perangkat Daerah
    Route::get('/layanandigital', [PerangkatDaerahServiceController::class, 'index'])->name('layanandigital');
    Route::post('/layanandigital', [PerangkatDaerahServiceController::class, 'store'])->name('layanandigital.store');
    Route::put('/layanandigital/{id}', [PerangkatDaerahServiceController::class, 'update'])->name('layanandigital.update');
    Route::delete('/layanandigital/{id}', [PerangkatDaerahServiceController::class, 'destroy'])->name('layanandigital.destroy');
    Route::get('/layanandigital/document/all', [ServiceDocumentController::class, 'downloadAllForDepartment'])->name('layanandigital.document-all');
    Route::get('/layanandigital/{service}/document', [ServiceDocumentController::class, 'downloadForDepartment'])->name('layanandigital.document');

    // 4. Kelola Teknisi (Fitur Baru Admin Dinas)
    Route::get('/teknisi', [PerangkatDaerahTechnicianController::class, 'index'])->name('teknisi.index');
    Route::post('/teknisi', [PerangkatDaerahTechnicianController::class, 'store'])->name('teknisi.store');
    Route::delete('/teknisi/{id}', [PerangkatDaerahTechnicianController::class, 'destroy'])->name('teknisi.destroy');

    // 5. Evaluasi SLA Perangkat Daerah & Cetak Laporan
    Route::get('/evaluasi', [PerangkatDaerahEvaluasiController::class, 'index'])->name('evaluasi.index');
    Route::get('/evaluasi/cetak', [PerangkatDaerahEvaluasiController::class, 'printReport'])->name('evaluasi.cetak');
});

/*
|--------------------------------------------------------------------------
| TEKNISI ROUTES (Dashboard & Ticketing Teknisi Dinas)
|--------------------------------------------------------------------------
*/
Route::prefix('teknisi')->name('teknisi.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Teknisi\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/ticketing', [\App\Http\Controllers\Teknisi\DashboardController::class, 'ticketing'])->name('ticketing');
    Route::match(['post', 'put'], '/tickets/{id}/update-status', [\App\Http\Controllers\Teknisi\DashboardController::class, 'updateStatus'])->name('tickets.update-status');
});