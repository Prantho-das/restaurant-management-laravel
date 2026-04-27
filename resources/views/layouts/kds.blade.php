<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kitchen Display System (KDS)</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            background-color: #111827; /* Dark Gray for less eye strain in kitchen */
            color: #f3f4f6;
        }
        /* Hide scrollbar for a cleaner KDS look */
        ::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body class="font-sans antialiased h-screen overflow-hidden flex flex-col">
    <!-- KDS Header -->
    <header class="bg-gray-900 border-b border-gray-800 p-4 flex justify-between items-center shadow-md">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold tracking-wider text-white">KITCHEN DISPLAY</h1>
            <span class="bg-blue-600 text-xs px-2 py-1 rounded text-white font-bold animate-pulse" id="kds-status">LIVE</span>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-gray-400 font-mono text-xl" id="kds-clock">--:--:--</div>
            <a href="{{ route('admin') }}" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 border border-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Exit
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-x-auto overflow-y-hidden p-6 w-full">
        {{ $slot }}
    </main>

    @livewireScripts
    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('kds-clock').innerText = now.toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
