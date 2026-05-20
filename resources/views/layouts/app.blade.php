<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NutriGo') - NutriGo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen" x-data>

<div class="flex h-screen overflow-hidden">

    {{-- Notifikasi (live update dengan Alpine.js) --}}
<div x-data="notifBadge()" x-init="fetchCount()" class="relative">
    <a href="{{ route('user.notifications') }}" class="relative inline-block">
        <span class="text-2xl">🔔</span>
        <span x-show="count > 0"
            x-text="count > 9 ? '9+' : count"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold leading-none">
        </span>
    </a>
</div>

<script>
function notifBadge() {
    return {
        count: {{ auth()->user()->notifications()->where('is_read', false)->count() }},
        async fetchCount() {
            // Refresh setiap 60 detik
            setInterval(async () => {
                try {
                    const res  = await fetch('/api/notifications/unread-count', {
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
                    });
                    const data = await res.json();
                    this.count = data.count;
                } catch(e) {}
            }, 60000);
        }
    }
}
</script>

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-ng-dark-green flex-shrink-0 flex flex-col">
        {{-- Logo --}}
        <div class="p-6 border-b border-green-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-lg">N</span>
                </div>
                <span class="text-white font-extrabold text-xl tracking-wide">nutri<span class="text-ng-yellow">Go</span></span>
            </div>
        </div>

        {{-- User Info --}}
        <div class="p-4 border-b border-green-700">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-ng-yellow flex items-center justify-center text-gray-800 font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->nickname ?? auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-white text-sm font-semibold">{{ auth()->user()->nickname ?? auth()->user()->name }}</p>
                    <p class="text-green-300 text-xs">{{ auth()->user()->daily_calorie_needs ?? '—' }} kcal/hari</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-4 overflow-y-auto">
            @php
            $navItems = [
    ['route' => 'user.dashboard',      'icon' => '🏠', 'label' => 'Dashboard'],
    ['route' => 'user.menu',           'icon' => '🍽️', 'label' => 'Menu'],
    ['route' => 'user.history',        'icon' => '📋', 'label' => 'Riwayat'],
    ['route' => 'user.notifications',  'icon' => '🔔', 'label' => 'Notifikasi'],
    ['route' => 'user.profile',        'icon' => '👤', 'label' => 'Profil'],
];
            @endphp
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-5 py-3 text-sm transition-all duration-150
                        {{ request()->routeIs($item['route']) ? 'bg-orange-500 text-white font-semibold' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                    <span>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-green-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-3 text-green-300 hover:text-white text-sm w-full px-2 py-2 rounded-lg hover:bg-green-800 transition">
                    <span>🚪</span><span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top Bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div class="flex items-center gap-4">
                {{-- Notifikasi --}}
                <a href="{{ route('user.profile') }}" class="relative">
                    <span class="text-2xl">🔔</span>
                    @if(auth()->user()->notifications()->where('is_read',false)->count() > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            {{ auth()->user()->notifications()->where('is_read',false)->count() }}
                        </span>
                    @endif
                </a>
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-4 flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-4">
                    @foreach($errors->all() as $error)
                        <p class="text-sm">⚠️ {{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto px-6 pb-6">
            @yield('content')
        </main>
    </div>
</div>
<x-flash />
</body>
</html>