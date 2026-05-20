@extends('layouts.app')
@section('title','Profil')
@section('page-title','Profil Saya')

@section('content')
<div class="py-4 space-y-6">

    {{-- ── HEADER PROFIL ────────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-ng-dark-green to-green-700 rounded-2xl p-6 text-white">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-full bg-ng-yellow flex items-center justify-center text-3xl font-extrabold text-gray-800">
                {{ strtoupper(substr($user->nickname ?? $user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ $user->nickname ?? $user->name }}</h2>
                <p class="text-green-300 text-sm">{{ $user->email }}</p>
                <p class="text-green-300 text-sm mt-1">📍 {{ $user->city }}, {{ $user->province }}</p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 mt-5">
            <div class="bg-green-800 rounded-xl p-3 text-center">
                <p class="text-2xl font-extrabold text-ng-yellow">{{ $user->bmi ?? '—' }}</p>
                <p class="text-green-300 text-xs">BMI</p>
                <p class="text-xs text-white font-medium">{{ $bmiCategory }}</p>
            </div>
            <div class="bg-green-800 rounded-xl p-3 text-center">
                <p class="text-2xl font-extrabold text-ng-yellow">{{ number_format($user->daily_calorie_needs ?? 0) }}</p>
                <p class="text-green-300 text-xs">Kalori/hari</p>
            </div>
            <div class="bg-green-800 rounded-xl p-3 text-center">
                <p class="text-2xl font-extrabold text-ng-yellow">{{ $user->getAge() }}</p>
                <p class="text-green-300 text-xs">Tahun</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── DATA KESEHATAN ───────────────────────────────── --}}
        <div class="card">
            <h3 class="font-bold text-gray-800 mb-4">📊 Data Kesehatan</h3>
            <form method="POST" action="{{ route('user.profile.health') }}">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 block mb-1">Tinggi (cm)</label>
                        <input type="number" name="height_cm" value="{{ $user->height_cm }}"
                               class="input-field" min="50" max="300" step="0.1" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 block mb-1">Berat (kg)</label>
                        <input type="number" name="weight_kg" value="{{ $user->weight_kg }}"
                               class="input-field" min="10" max="500" step="0.1" required>
                    </div>
                </div>

                {{-- BMI Analysis --}}
                @if($user->bmi)
                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <p class="text-sm font-semibold text-gray-700 mb-2">BMI Analysis</p>
                        <div class="relative h-3 bg-gray-200 rounded-full mb-2">
                            @php
                                $bmiPct = min(100, max(0, (($user->bmi - 10) / 30) * 100));
                                $bmiColor = match(true) {
                                    $user->bmi < 18.5 => 'bg-blue-400',
                                    $user->bmi < 25   => 'bg-green-500',
                                    $user->bmi < 30   => 'bg-yellow-400',
                                    default           => 'bg-red-500',
                                };
                            @endphp
                            <div class="absolute h-3 rounded-full {{ $bmiColor }}"
                                 style="width: {{ $bmiPct }}%"></div>
                            <div class="absolute h-5 w-1 bg-gray-800 rounded top-[-4px]"
                                 style="left: calc({{ $bmiPct }}% - 2px)"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>10</span><span class="text-blue-500">18.5</span>
                            <span class="text-green-500">25</span><span class="text-yellow-500">30</span><span class="text-red-500">40</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <p class="text-3xl font-extrabold {{ $bmiColor === 'bg-green-500' ? 'text-green-600' : ($bmiColor === 'bg-blue-400' ? 'text-blue-500' : ($bmiColor === 'bg-yellow-400' ? 'text-yellow-600' : 'text-red-600')) }}">{{ $user->bmi }}</p>
                            <span class="badge-orange text-sm">{{ $bmiCategory }}</span>
                        </div>
                        @if($user->bmi > 25)
                            <p class="text-xs text-gray-500 mt-2">💡 Coba tambah jalan kaki ringan 30 menit/hari untuk mendekati BMI ideal (18.5–24.9)</p>
                        @elseif($user->bmi < 18.5)
                            <p class="text-xs text-gray-500 mt-2">💡 Coba tambah konsumsi makanan bergizi untuk mencapai berat badan ideal</p>
                        @else
                            <p class="text-xs text-green-600 mt-2">✅ BMI kamu dalam kategori ideal! Pertahankan pola makan sehat.</p>
                        @endif
                    </div>
                @endif

                <div class="mb-4">
                    <label class="text-xs font-semibold text-gray-600 block mb-2">Level Aktivitas</label>
                    <select name="activity_level" class="input-field">
                        @foreach([
                            'sedentary'   => '💻 Ringan (duduk, kerja laptop)',
                            'light'       => '🚶 Sedikit aktif (jalan kaki ringan)',
                            'moderate'    => '🏃 Cukup aktif (olahraga 3-5x/minggu)',
                            'active'      => '⚡ Aktif (olahraga intensif)',
                            'very_active' => '🏋️ Sangat aktif (kerja fisik/gym rutin)',
                        ] as $val => $label)
                            <option value="{{ $val }}" {{ $user->activity_level == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="btn-primary w-full">💾 Simpan Data Kesehatan</button>
            </form>
        </div>

        {{-- ── ALERGI MAKANAN ───────────────────────────────── --}}
        <div class="card">
            <h3 class="font-bold text-gray-800 mb-4">🚨 Alergi Makanan</h3>
            <form method="POST" action="{{ route('user.profile.allergies') }}">
                @csrf
                <div class="grid grid-cols-2 gap-2 mb-4">
                    @foreach(['Seafood','Udang','Kepiting','Cumi','Kacang','Susu','Telur','Gluten','Kedelai','Gandum'] as $allergen)
                        @php $isActive = $allergies->pluck('allergen')->contains(strtolower($allergen)); @endphp
                        <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all
                            {{ $isActive ? 'border-red-300 bg-red-50' : 'border-gray-200 hover:border-red-200 hover:bg-red-50' }}">
                            <input type="checkbox" name="allergens[]"
                                   value="{{ strtolower($allergen) }}"
                                   {{ $isActive ? 'checked' : '' }}
                                   class="rounded text-red-500">
                            <span class="text-sm text-gray-700">{{ $allergen }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mb-4">
                    <label class="text-xs font-semibold text-gray-600 block mb-1">Tambah alergi lainnya</label>
                    <input type="text" name="custom_allergy" class="input-field"
                           placeholder="Cth: durian, pete..." value="">
                </div>
                <button class="btn-primary w-full">💾 Simpan Alergi</button>
            </form>
        </div>

        {{-- ── PENGINGAT MAKAN ───────────────────────────────── --}}
        <div class="card">
            <h3 class="font-bold text-gray-800 mb-4">⏰ Pengingat Makan</h3>
            <form method="POST" action="{{ route('user.profile.reminders') }}">
                @csrf
                @foreach([
                    'breakfast' => ['label'=>'Sarapan','icon'=>'🌅'],
                    'lunch'     => ['label'=>'Makan Siang','icon'=>'☀️'],
                    'dinner'    => ['label'=>'Makan Malam','icon'=>'🌙'],
                ] as $type => $info)
                    @php $reminder = $reminders->where('meal_type', $type)->first(); @endphp
                    <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">
                        <span class="text-2xl">{{ $info['icon'] }}</span>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-700 text-sm">{{ $info['label'] }}</p>
                            <input type="time" name="{{ $type }}_time"
                                   value="{{ $reminder ? substr($reminder->reminder_time, 0, 5) : '07:00' }}"
                                   class="input-field text-sm mt-1">
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="{{ $type }}_active"
                                   {{ $reminder?->is_active ? 'checked' : '' }}
                                   class="rounded text-ng-green w-5 h-5">
                            <span class="text-xs text-gray-600">Aktif</span>
                        </label>
                    </div>
                @endforeach
                <button class="btn-primary w-full mt-4">💾 Simpan Pengingat</button>
            </form>
        </div>

        {{-- ── GANTI PASSWORD ─────────────────────────────────── --}}
        <div class="card">
            <h3 class="font-bold text-gray-800 mb-4">🔒 Ganti Password</h3>
            <form method="POST" action="{{ route('user.profile.password') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 block mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" class="input-field" required>
                        @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 block mb-1">Password Baru</label>
                        <input type="password" name="password" class="input-field" required minlength="8">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 block mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="input-field" required>
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button class="btn-primary w-full">🔑 Ganti Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection