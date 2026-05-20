@extends('layouts.admin')
@section('title','Detail User')
@section('page-title','Detail User')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:underline">← Kembali ke daftar user</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Profile Card --}}
    <div class="card lg:col-span-1">
        <div class="text-center mb-4">
            <div class="w-20 h-20 rounded-full bg-ng-yellow flex items-center justify-center text-3xl font-extrabold text-gray-800 mx-auto">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2 class="font-bold text-xl text-gray-800 mt-3">{{ $user->name }}</h2>
            <p class="text-gray-500 text-sm">{{ $user->email }}</p>
            @if($user->nickname)
                <p class="text-xs text-gray-400">Dipanggil: <span class="font-medium">{{ $user->nickname }}</span></p>
            @endif
        </div>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Tanggal Lahir</span><span class="font-medium">{{ $user->birth_date?->isoFormat('D MMMM Y') ?? '—' }}</span></div>
            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Usia</span><span class="font-medium">{{ $user->getAge() }} tahun</span></div>
            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Gender</span><span class="font-medium">{{ $user->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</span></div>
            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Wilayah</span><span class="font-medium text-right">{{ $user->city }}, {{ $user->province }}</span></div>
            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Tinggi</span><span class="font-medium">{{ $user->height_cm }} cm</span></div>
            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Berat</span><span class="font-medium">{{ $user->weight_kg }} kg</span></div>
            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">BMI</span><span class="font-bold text-ng-orange">{{ $user->bmi }}</span></div>
            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Kalori/hari</span><span class="font-bold text-ng-green">{{ number_format($user->daily_calorie_needs ?? 0) }} kcal</span></div>
            <div class="flex justify-between py-2"><span class="text-gray-500">Aktivitas</span><span class="font-medium">{{ ucfirst($user->activity_level ?? '—') }}</span></div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-5">
        {{-- Alergi --}}
        <div class="card">
            <h3 class="font-bold text-gray-800 mb-3">🚨 Alergi Makanan</h3>
            @if($user->allergies->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($user->allergies as $a)
                        <span class="bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-full font-medium">🚫 {{ $a->allergen }}</span>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm">Tidak ada alergi tercatat</p>
            @endif
        </div>

        {{-- Riwayat Makan Terbaru --}}
        <div class="card">
            <h3 class="font-bold text-gray-800 mb-3">📋 Riwayat Makan Terbaru (10 entri)</h3>
            @if($user->foodHistories->isNotEmpty())
                <div class="space-y-2">
                    @foreach($user->foodHistories->take(10) as $h)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $h->food?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($h->meal_type) }} · {{ \Carbon\Carbon::parse($h->consumed_date)->isoFormat('D MMM Y') }}</p>
                            </div>
                            <span class="text-sm font-bold text-ng-orange">{{ $h->calories_consumed }} kcal</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm">Belum ada riwayat makan</p>
            @endif
        </div>
    </div>
</div>
@endsection