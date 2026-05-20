@extends('layouts.admin')
@section('title','Dashboard Admin')
@section('page-title','Dashboard Admin')

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Total User','value'=>$stats['total_users'],'icon'=>'👥','color'=>'bg-blue-500'],
            ['label'=>'Data Makanan','value'=>$stats['total_foods'],'icon'=>'🥗','color'=>'bg-green-500'],
            ['label'=>'Artikel','value'=>$stats['total_articles'],'icon'=>'📰','color'=>'bg-purple-500'],
            ['label'=>'Log Makan Hari Ini','value'=>$stats['total_logs'],'icon'=>'📊','color'=>'bg-orange-500'],
        ] as $stat)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="{{ $stat['color'] }} w-12 h-12 rounded-xl flex items-center justify-center text-2xl">
                    {{ $stat['icon'] }}
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $stat['value'] }}</p>
                    <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Users --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">User Terbaru</h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-ng-orange">Lihat Semua →</a>
            </div>
            <div class="space-y-3">
                @foreach($recentUsers as $u)
                    <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                        <div class="w-9 h-9 rounded-full bg-ng-yellow flex items-center justify-center font-bold text-sm text-gray-800">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-700">{{ $u->name }}</p>
                            <p class="text-xs text-gray-500">{{ $u->email }}</p>
                        </div>
                        <span class="{{ $u->onboarding_completed ? 'badge-green' : 'badge-orange' }}">
                            {{ $u->onboarding_completed ? 'Aktif' : 'Setup' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top Foods --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4">🏆 Makanan Paling Sering Dikonsumsi</h3>
            <div class="space-y-3">
                @foreach($topFoods as $i => $tf)
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-gray-400 w-5">{{ $i+1 }}</span>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-700">{{ $tf->food?->name ?? '—' }}</p>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                                <div class="bg-orange-500 h-1.5 rounded-full" style="width: {{ min(100, ($tf->count / max(1, $topFoods->first()->count)) * 100) }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500">{{ $tf->count }}x</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection