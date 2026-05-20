@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="py-4 space-y-6">

    {{-- Hero Card --}}
    <div class="bg-ng-dark-green rounded-2xl p-6 text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-green-300 text-sm font-medium">👋 Hai, {{ $user->nickname ?? $user->name }}!</p>
                <h2 class="text-2xl font-bold mt-1">Menu hari ini sudah<br>disesuaikan untukmu 🎯</h2>
                <a href="{{ route('user.menu') }}" class="mt-4 inline-flex items-center gap-2 bg-ng-yellow text-gray-900 px-5 py-2.5 rounded-full text-sm font-bold hover:bg-yellow-400 transition">
                    Lihat Rekomendasi →
                </a>
            </div>
            <div class="text-right hidden sm:block">
                <div class="grid grid-cols-3 gap-3 mt-2">
                    @foreach(['Sarapan' => $recommendation->breakfast, 'Makan Siang' => $recommendation->lunch, 'Makan Malam' => $recommendation->dinner] as $label => $food)
                        <div class="bg-green-800 rounded-xl p-3 text-center">
                            <p class="text-green-300 text-xs">{{ $label }}</p>
                            <p class="text-white text-xs font-semibold mt-1">{{ $food?->calories ?? '—' }} kcal</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card text-center">
            <p class="text-3xl font-extrabold text-ng-orange">{{ $user->bmi ?? '—' }}</p>
            <p class="text-sm text-gray-500 mt-1">BMI Kamu</p>
            @if($user->bmi)
                @php
                    $bmiCat = match(true) {
                        $user->bmi < 18.5 => ['Kurus','text-blue-500'],
                        $user->bmi < 25   => ['Normal','text-green-500'],
                        $user->bmi < 30   => ['Overweight','text-yellow-500'],
                        default           => ['Obesitas','text-red-500'],
                    };
                @endphp
                <span class="text-xs font-semibold {{ $bmiCat[1] }}">{{ $bmiCat[0] }}</span>
            @endif
        </div>
        <div class="card text-center">
            <p class="text-3xl font-extrabold text-ng-green">{{ number_format($user->daily_calorie_needs ?? 0) }}</p>
            <p class="text-sm text-gray-500 mt-1">Kalori/Hari</p>
            <span class="text-xs text-gray-400">Target AKG</span>
        </div>
        <div class="card text-center">
            <p class="text-3xl font-extrabold text-blue-500">{{ number_format($todayCalories) }}</p>
            <p class="text-sm text-gray-500 mt-1">Dikonsumsi Hari Ini</p>
            @if($user->daily_calorie_needs)
                <span class="text-xs {{ $todayCalories > $user->daily_calorie_needs ? 'text-red-500' : 'text-green-500' }}">
                    {{ $todayCalories > $user->daily_calorie_needs ? '⚠️ Melebihi' : '✅ Dalam batas' }}
                </span>
            @endif
        </div>
        <div class="card text-center">
            <p class="text-3xl font-extrabold text-purple-500">{{ strtoupper(substr($user->activity_level ?? 'moderate', 0, 3)) }}</p>
            <p class="text-sm text-gray-500 mt-1">Level Aktivitas</p>
            <span class="text-xs text-gray-400">{{ ucfirst($user->activity_level ?? '—') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Menu Hari Ini --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">Menu Hari Ini</h3>
                <a href="{{ route('user.menu') }}" class="text-sm text-ng-orange font-semibold">Lihat Semua →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach([
                    ['label'=>'Sarapan','icon'=>'🌅','food'=>$recommendation->breakfast,'type'=>'breakfast'],
                    ['label'=>'Makan Siang','icon'=>'☀️','food'=>$recommendation->lunch,'type'=>'lunch'],
                    ['label'=>'Makan Malam','icon'=>'🌙','food'=>$recommendation->dinner,'type'=>'dinner'],
                ] as $meal)
                    <div class="card hover:shadow-md transition">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xl">{{ $meal['icon'] }}</span>
                            <span class="text-sm font-semibold text-gray-600">{{ $meal['label'] }}</span>
                        </div>
                        @if($meal['food'])
                            <p class="font-bold text-gray-800 text-sm">{{ $meal['food']->name }}</p>
                            <p class="text-ng-orange font-semibold text-sm mt-1">{{ $meal['food']->calories }} kcal</p>
                            <div class="text-xs text-gray-500 mt-2 space-y-0.5">
                                <p>Protein: {{ $meal['food']->proteins }}g</p>
                                <p>Lemak: {{ $meal['food']->fat }}g</p>
                                <p>Karbo: {{ $meal['food']->carbohydrate }}g</p>
                            </div>
                            <form method="POST" action="{{ route('user.menu.log') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="food_id" value="{{ $meal['food']->id }}">
                                <input type="hidden" name="meal_type" value="{{ $meal['type'] }}">
                                <button class="text-xs bg-orange-500 text-white px-3 py-1.5 rounded-full w-full hover:bg-orange-700 transition">
                                    Log Makan
                                </button>
                            </form>
                        @else
                            <p class="text-gray-400 text-sm">Tidak tersedia</p>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pengingat Makan --}}
            <div class="card">
                <h4 class="font-bold text-gray-800 mb-4">⏰ Pengingat Makan</h4>
                <div class="space-y-3">
                    @foreach($reminders as $r)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full {{ $r->is_active ? 'bg-ng-green' : 'bg-gray-300' }}"></div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">{{ ucfirst($r->meal_type === 'breakfast' ? 'Sarapan' : ($r->meal_type === 'lunch' ? 'Makan Siang' : 'Makan Malam')) }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($r->reminder_time)->format('H:i') }}</p>
                                </div>
                            </div>
                            <span class="{{ $r->is_active ? 'badge-green' : 'badge-orange' }}">{{ $r->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Artikel Kesehatan --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Artikel Kesehatan</h3>
            </div>
            <div class="space-y-3">
                @forelse($articles as $article)
                    <div class="card hover:shadow-md transition cursor-pointer">
                        <span class="badge-green">{{ ucfirst($article->category) }}</span>
                        <h4 class="font-bold text-gray-800 text-sm mt-2 line-clamp-2">{{ $article->title }}</h4>
                        <p class="text-xs text-gray-500 mt-2">{{ $article->read_time }} menit baca</p>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">Belum ada artikel</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection