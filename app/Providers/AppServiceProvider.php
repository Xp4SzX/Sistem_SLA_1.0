<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $publicStoragePath = public_path('storage');
        $storageTargetPath = storage_path('app/public');

        if (!is_dir($publicStoragePath) && !is_link($publicStoragePath)) {
            try {
                symlink($storageTargetPath, $publicStoragePath);
            } catch (\Throwable $e) {
                // Ignore symlink creation errors on hosts that disallow it; uploaded files can still be stored in the app/public path.
            }
        }

        // Bagikan data Notifikasi Tiket ke Navbar untuk User yang sedang Login
        View::composer('layouts.partials.navbar', function ($view) {
            if (Auth::check()) {
                $tickets = Ticket::where('user_id', Auth::id())
                    ->latest()
                    ->get();
                $view->with('tickets', $tickets);
            } else {
                $view->with('tickets', collect());
            }
        });
    }
}