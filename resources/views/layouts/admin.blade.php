<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'NutriGo')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen" x-data>
<div class="flex h-screen overflow-hidden">

    {{-- ADMIN SIDEBAR --}}
    <aside class="w-64 bg-gray-900 flex-shrink-0 flex flex-col">
        <div class="p-6 border-b border-gray-700">
            <span class="text-white font-extrabold text-xl">nutri<span class="text-ng-yellow">Go</span></span>
            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">ADMIN</span>
        </div>

        <nav class="flex-1 py-4">
            @php
                $adminNav = [
                    ['route'=>'admin.dashboard',       'icon'=>'📊', 'label'=>'Dashboard'],
                    ['route'=>'admin.users.index',      'icon'=>'👥', 'label'=>'Kelola User'],
                    ['route'=>'admin.foods.index',      'icon'=>'🥗', 'label'=>'Data Makanan'],
                    ['route'=>'admin.articles.index',   'icon'=>'📰', 'label'=>'Artikel'],
                ];
            @endphp
            @foreach($adminNav as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-5 py-3 text-sm transition-all
                          {{ request()->routeIs($item['route']) ? 'bg-orange-500 text-white font-semibold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span>{{ $item['icon'] }}</span><span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="p-4 border-t border-gray-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-gray-400 hover:text-white text-sm flex items-center gap-2">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b px-6 py-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Admin Panel')</h1>
            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
        </header>

        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-4">
                    ✅ {{ session('success') }}
                </div>
            @endif
        </div>

        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>
<x-flash />
</body>
</html>