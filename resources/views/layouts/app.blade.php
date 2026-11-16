<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SLA Helpdesk - Pemkab Bandung Barat' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-[#F8FAFC] antialiased" x-data="{ sidebarOpen: true, detailOpen: false }">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Navbar -->
            @include('layouts.partials.navbar')

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6 bg-[#F8FAFC]">
                {{ $slot }}
            </main>
        </div>
    </div>

</body>
</html>