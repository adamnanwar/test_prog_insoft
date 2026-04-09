<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-56 bg-white border-r border-gray-100 flex flex-col py-8 px-4 shrink-0 min-h-screen">
        <div class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-8 px-2">App</div>
        <nav class="flex flex-col gap-1 flex-1">
            <a href="{{ route('barang') }}"
               class="px-2 py-2 text-sm flex items-center gap-2 {{ request()->routeIs('barang') ? 'font-bold text-black border-l-4 border-black pl-1.5' : 'text-gray-400 hover:text-gray-600' }}">
                Barang
            </a>
        </nav>
        <a href="{{ route('logout') }}"
           class="px-2 py-2 text-sm text-gray-400 hover:text-black transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/>
            </svg>
            Logout
        </a>
    </aside>

    {{-- Main content --}}
    <main class="flex-1 p-8 overflow-auto">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
