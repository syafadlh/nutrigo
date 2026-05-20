@extends('layouts.app')
@section('title','Riwayat')
@section('page-title','Riwayat Makan')

@section('content')
<div class="py-4 space-y-6">

    {{-- ── FILTER PERIODE ──────────────────────────────────── --}}
    <div class="flex gap-2 flex-wrap">
        @foreach(['today'=>'Hari Ini','week'=>'7 Hari','month'=>'30 Hari'] as $key => $label)
            <a href="{{ route('user.history', ['period' => $key]) }}"
               class="px-4 py-2 rounded-full text-sm font-semibold transition-all
                      {{ $period == $key ? 'bg-ng-orange text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-ng-orange hover:text-ng-orange' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- ── RINGKASAN STATISTIK ──────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $totalKal = $dailySummary->sum('total_calories');
            $avgKal   = $dailySummary->count() > 0 ? $dailySummary->avg('total_calories') : 0;
            $maxDay   = $dailySummary->max('total_calories') ?? 0;
            $totalMakanan = $histories->total();
        @endphp
        <div class="card text-center">
            <p class="text-2xl font-extrabold text-ng-orange">{{ number_format($totalKal) }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Kalori</p>
        </div>
        <div class="card text-center">
            <p class="text-2xl font-extrabold text-ng-green">{{ number_format($avgKal) }}</p>
            <p class="text-xs text-gray-500 mt-1">Rata-rata/Hari</p>
            <p class="text-xs {{ $avgKal > ($user->daily_calorie_needs ?? 2000) ? 'text-red-500' : 'text-green-500' }}">
                Target: {{ number_format($user->daily_calorie_needs ?? 2000) }} kcal
            </p>
        </div>
        <div class="card text-center">
            <p class="text-2xl font-extrabold text-blue-500">{{ number_format($maxDay) }}</p>
            <p class="text-xs text-gray-500 mt-1">Tertinggi/Hari</p>
        </div>
        <div class="card text-center">
            <p class="text-2xl font-extrabold text-purple-500">{{ $totalMakanan }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Entri</p>
        </div>
    </div>

    {{-- ── GRAFIK KALORI HARIAN ─────────────────────────────── --}}
    @if($dailySummary->count() > 0)
        <div class="card">
            <h3 class="font-bold text-gray-800 mb-4">📊 Grafik Kalori Harian</h3>
            <div style="height: 200px; position: relative;" class="w-full">
                <canvas id="calorieChart"></canvas>
            </div>
        </div>
    @endif

    {{-- ── RINGKASAN PER HARI ───────────────────────────────── --}}
    @if($dailySummary->count() > 0)
        <div class="card">
            <h3 class="font-bold text-gray-800 mb-4">📅 Ringkasan Per Hari</h3>
            <div class="space-y-3">
                @foreach($dailySummary as $day)
                    @php
                        $target = $user->daily_calorie_needs ?? 2000;
                        $pct    = min(100, ($day->total_calories / $target) * 100);
                        $over   = $day->total_calories > $target;
                    @endphp
                    <div class="flex items-center gap-4">
                        <div class="w-24 flex-shrink-0">
                            <p class="text-sm font-semibold text-gray-700">
                                {{ \Carbon\Carbon::parse($day->consumed_date)->isoFormat('D MMM') }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $day->meals }} makanan</p>
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-100 rounded-full h-3">
                                <div class="h-3 rounded-full {{ $over ? 'bg-red-400' : 'bg-ng-green' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        <div class="w-24 text-right flex-shrink-0">
                            <p class="text-sm font-bold {{ $over ? 'text-red-500' : 'text-ng-green' }}">
                                {{ number_format($day->total_calories) }}
                            </p>
                            <p class="text-xs text-gray-400">kcal</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── DETAIL RIWAYAT ───────────────────────────────────── --}}
    <div class="card">
        <h3 class="font-bold text-gray-800 mb-4">🍽️ Detail Riwayat Makan</h3>

        @forelse($histories as $history)
            @php
                $mealIcons = ['breakfast'=>'🌅','lunch'=>'☀️','dinner'=>'🌙','snack'=>'🍪'];
                $mealLabels= ['breakfast'=>'Sarapan','lunch'=>'Makan Siang','dinner'=>'Makan Malam','snack'=>'Snack'];
            @endphp
            <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0 group">
                <div class="text-2xl">{{ $mealIcons[$history->meal_type] ?? '🍽️' }}</div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800 text-sm">{{ $history->food?->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $mealLabels[$history->meal_type] ?? $history->meal_type }}
                        · {{ \Carbon\Carbon::parse($history->consumed_date)->isoFormat('D MMM Y') }}
                        @if($history->consumed_time)
                            · {{ \Carbon\Carbon::parse($history->consumed_time)->format('H:i') }}
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-ng-orange text-sm">{{ $history->calories_consumed }} kcal</p>
                    @if($history->food)
                        <p class="text-xs text-gray-400">{{ $history->food->proteins }}g protein</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('user.history.destroy', $history) }}"
                      onsubmit="return confirm('Hapus riwayat ini?')"
                      class="opacity-0 group-hover:opacity-100 transition-opacity">
                    @csrf @method('DELETE')
                    <button class="text-red-400 hover:text-red-600 text-xs p-1">🗑️</button>
                </form>
            </div>
        @empty
            <div class="text-center py-10 text-gray-400">
                <p class="text-4xl mb-3">📋</p>
                <p class="font-semibold">Belum ada riwayat makan</p>
                <p class="text-sm">Mulai log makananmu dari halaman Menu</p>
                <a href="{{ route('user.menu') }}" class="btn-primary inline-block mt-4 text-sm">Ke Halaman Menu →</a>
            </div>
        @endforelse

        <div class="mt-4">{{ $histories->withQueryString()->links() }}</div>
    </div>
</div>

{{-- Chart.js untuk grafik --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
@if($dailySummary->count() > 0)
const ctx = document.getElementById('calorieChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! $dailySummary->pluck('consumed_date')->map(fn($d) => \Carbon\Carbon::parse($d)->isoFormat('D MMM'))->toJson() !!},
        datasets: [{
            label: 'Kalori (kcal)',
            data: {!! $dailySummary->pluck('total_calories')->toJson() !!},
            backgroundColor: {!! $dailySummary->map(fn($d) => $d->total_calories > ($user->daily_calorie_needs ?? 2000) ? 'rgba(239,68,68,0.7)' : 'rgba(106,176,76,0.7)')->toJson() !!},
            borderRadius: 6,
        },{
            label: 'Target',
            data: Array({{ $dailySummary->count() }}).fill({{ $user->daily_calorie_needs ?? 2000 }}),
            type: 'line',
            borderColor: '#e8601a',
            borderWidth: 2,
            borderDash: [5,5],
            pointRadius: 0,
            fill: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});
@endif
</script>
@endsection