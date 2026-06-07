@extends('layouts.app')
@section('title', 'Riwayat')
@section('page-title', 'Riwayat Makan')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        <div
            class="rounded-[32px] bg-gradient-to-r from-[#18542A] via-[#236937] to-[#9ABC05] p-8 text-white shadow-[0_20px_50px_rgba(24,84,42,0.18)]">
            <span
                class="rounded-full bg-[#FFC926] px-4 py-1 text-xs font-extrabold uppercase tracking-[0.2em] text-[#18542A]">
                Food History
            </span>
            <h1 class="mt-4 text-4xl font-black">Riwayat Makan</h1>
            <p class="mt-2 text-white/80">
                Pantau konsumsi kalori dan perkembangan pola makan harianmu.
            </p>
        </div>

        <div class="flex gap-2 flex-wrap">
            @foreach (['today' => 'Hari Ini', 'week' => '7 Hari', 'month' => '30 Hari'] as $key => $label)
                <a href="{{ route('user.history', ['period' => $key]) }}"
                    class="px-5 py-2 rounded-full text-sm font-bold transition-all duration-300
                      {{ $period == $key ? 'bg-[#F96015] text-white shadow-md' : 'bg-white border border-[#F3E8CC] text-[#18542A] hover:border-[#F96015] hover:text-[#F96015]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $totalKal = $dailySummary->sum('total_calories');
                $avgKal = $dailySummary->count() > 0 ? $dailySummary->avg('total_calories') : 0;
                $maxDay = $dailySummary->max('total_calories') ?? 0;
                $totalMakanan = $histories->total();
            @endphp

            <div class="rounded-[24px] bg-[#FFC926] p-5 text-center shadow-lg">
                <p class="text-2xl font-extrabold text-[#18542A]">{{ number_format($totalKal) }}</p>
                <p class="text-xs text-[#18542A]/70 mt-1">Total Kalori</p>
            </div>

            <div class="rounded-[24px] bg-[#9ABC05] p-5 text-center shadow-lg">
                <p class="text-2xl font-extrabold text-[#18542A]">{{ number_format($avgKal) }}</p>
                <p class="text-xs text-[#18542A]/70 mt-1">Rata-rata/Hari</p>
                <p class="text-xs {{ $avgKal > ($user->daily_calorie_needs ?? 2000) ? 'text-red-700' : 'text-[#18542A]' }}">
                    Target: {{ number_format($user->daily_calorie_needs ?? 2000) }} kcal
                </p>
            </div>

            <div class="rounded-[24px] bg-[#F96015] p-5 text-center text-white shadow-lg">
                <p class="text-2xl font-extrabold">{{ number_format($maxDay) }}</p>
                <p class="text-xs text-white/80 mt-1">Tertinggi/Hari</p>
            </div>

            <div class="rounded-[24px] bg-[#18542A] p-5 text-center text-white shadow-lg">
                <p class="text-2xl font-extrabold">{{ $totalMakanan }}</p>
                <p class="text-xs text-white/80 mt-1">Total Entri</p>
            </div>
        </div>

        @if ($dailySummary->count() > 0)
            <div class="rounded-[28px] bg-white p-6 shadow-[0_15px_35px_rgba(24,84,42,0.08)]">
                <h3 class="mb-4 text-lg font-black text-[#18542A]">Grafik Kalori Harian</h3>
                <div style="height: 200px; position: relative;" class="w-full">
                    <canvas id="calorieChart"></canvas>
                </div>
            </div>
        @endif

        @if ($dailySummary->count() > 0)
            <div class="rounded-[28px] bg-white p-6 shadow-[0_15px_35px_rgba(24,84,42,0.08)]">
                <h3 class="mb-4 text-lg font-black text-[#18542A]">Ringkasan Per Hari</h3>

                <div class="space-y-3">
                    @foreach ($dailySummary as $day)
                        @php
                            $target = $user->daily_calorie_needs ?? 2000;
                            $pct = min(100, ($day->total_calories / $target) * 100);
                            $over = $day->total_calories > $target;
                        @endphp

                        <div class="flex items-center gap-4 rounded-2xl p-3 transition-all duration-300 hover:bg-[#FFF8EA]">
                            <div class="w-24 flex-shrink-0">
                                <p class="text-sm font-semibold text-gray-700">
                                    {{ \Carbon\Carbon::parse($day->consumed_date)->isoFormat('D MMM') }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $day->meals }} makanan</p>
                            </div>

                            <div class="flex-1">
                                <div class="bg-gray-100 rounded-full h-3">
                                    <div class="h-3 rounded-full {{ $over ? 'bg-[#D52518]' : 'bg-[#9ABC05]' }}"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                            </div>

                            <div class="w-24 text-right flex-shrink-0">
                                <p class="text-sm font-bold {{ $over ? 'text-[#D52518]' : 'text-[#18542A]' }}">
                                    {{ number_format($day->total_calories) }}
                                </p>
                                <p class="text-xs text-gray-400">kcal</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="rounded-[28px] bg-white p-6 shadow-[0_15px_35px_rgba(24,84,42,0.08)]">
            <h3 class="mb-5 text-lg font-black text-[#18542A]">Detail Riwayat Makan</h3>

            @forelse($histories as $history)
                @php
                    $mealIcons = ['breakfast' => '🌅', 'lunch' => '☀️', 'dinner' => '🌙', 'snack' => '🍪'];
                    $mealLabels = [
                        'breakfast' => 'Sarapan',
                        'lunch' => 'Makan Siang',
                        'dinner' => 'Makan Malam',
                        'snack' => 'Snack',
                    ];
                @endphp

                <div
                    class="group flex items-center gap-4 rounded-2xl p-4 transition-all duration-300 hover:bg-[#FFF8EA] border-b border-[#F3E8CC] last:border-0">
                    <div class="text-2xl">{{ $mealIcons[$history->meal_type] ?? '🍽️' }}</div>

                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm">{{ $history->food?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $mealLabels[$history->meal_type] ?? $history->meal_type }}
                            · {{ \Carbon\Carbon::parse($history->consumed_date)->isoFormat('D MMM Y') }}
                            @if ($history->consumed_time)
                                · {{ \Carbon\Carbon::parse($history->consumed_time)->format('H:i') }}
                            @endif
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="font-black text-[#F96015] text-sm">{{ $history->calories_consumed }} kcal</p>
                        @if ($history->food)
                            <p class="text-xs text-gray-400">{{ $history->food->proteins }}g protein</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('user.history.destroy', $history) }}"
                        onsubmit="return confirm('Hapus riwayat ini?')"
                        class="opacity-0 group-hover:opacity-100 transition-opacity">
                        @csrf @method('DELETE')
                        <button
                            class="rounded-full bg-red-50 p-2 text-red-500 transition hover:bg-red-100 hover:text-red-700">🗑</button>
                    </form>
                </div>

            @empty

                <div class="py-12 text-center">
                    <div class="text-7xl">🍽️</div>
                    <h3 class="mt-4 text-xl font-black text-[#18542A]">Belum Ada Riwayat Makan</h3>
                    <p class="mt-2 text-[#75684F]">Mulai catat makananmu dan pantau perkembangan nutrisi harianmu.</p>

                    <a href="{{ route('user.menu') }}"
                        class="mt-5 inline-flex rounded-full bg-[#F96015] px-5 py-3 font-bold text-white hover:bg-[#D52518]">
                        Pilih Menu →
                    </a>
                </div>
            @endforelse

            <div class="mt-4">{{ $histories->withQueryString()->links() }}</div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        @if ($dailySummary->count() > 0)
            const ctx = document.getElementById('calorieChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! $dailySummary->pluck('consumed_date')->map(fn($d) => \Carbon\Carbon::parse($d)->isoFormat('D MMM'))->toJson() !!},
                    datasets: [{
                        label: 'Kalori (kcal)',
                        data: {!! $dailySummary->pluck('total_calories')->toJson() !!},
                        backgroundColor: {!! $dailySummary->map(
                                fn($d) => $d->total_calories > ($user->daily_calorie_needs ?? 2000)
                                    ? 'rgba(213,37,24,0.7)'
                                    : 'rgba(154,188,5,0.7)',
                            )->toJson() !!},
                        borderRadius: 8,
                    }, {
                        label: 'Target',
                        data: Array({{ $dailySummary->count() }}).fill(
                            {{ $user->daily_calorie_needs ?? 2000 }}),
                        type: 'line',
                        borderColor: '#F96015',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        @endif
    </script>
@endsection