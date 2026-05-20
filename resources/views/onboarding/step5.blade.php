@extends('onboarding.layout')
@php $currentStep = 5; @endphp

@section('content')
<div class="bg-white rounded-3xl shadow-xl p-8">
    <div class="mb-8">
        <div class="bg-ng-cream rounded-2xl p-5 mb-6">
            <p class="font-bold text-ng-dark-green text-lg">📊 Yuk Hitung AKG Kamu!</p>
            <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                <strong>AKG (Angka Kecukupan Gizi)</strong> adalah jumlah energi dan zat gizi yang dibutuhkan
                seseorang setiap hari agar tubuh tetap sehat dan berfungsi optimal. Mengetahui AKG kamu
                membantu kami merekomendasikan menu yang pas — tidak kurang, tidak berlebihan. 💪
            </p>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Data Fisikmu</h2>
        <p class="text-gray-500 text-sm mt-1">Untuk menghitung kalori harian idealmu</p>
    </div>

    <form method="POST" action="{{ route('onboarding.save.5') }}">
        @csrf
        <div class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-2">Tinggi Badan (cm)</label>
                    <input type="number" name="height_cm" value="{{ old('height_cm') }}"
                           class="input-field" placeholder="165" min="50" max="300" step="0.1" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-2">Berat Badan (kg)</label>
                    <input type="number" name="weight_kg" value="{{ old('weight_kg') }}"
                           class="input-field" placeholder="60" min="10" max="500" step="0.1" required>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-2">Level Aktivitas Harianmu</label>
                <div class="space-y-2">
                    @foreach([
                        ['value'=>'sedentary',  'label'=>'Ringan (duduk, kerja laptop)', 'icon'=>'💻'],
                        ['value'=>'light',      'label'=>'Sedikit aktif (jalan kaki ringan)', 'icon'=>'🚶'],
                        ['value'=>'moderate',   'label'=>'Cukup aktif (olahraga 3-5x/minggu)', 'icon'=>'🏃'],
                        ['value'=>'active',     'label'=>'Aktif (olahraga intensif)', 'icon'=>'⚡'],
                        ['value'=>'very_active','label'=>'Sangat aktif (kerja fisik/gym rutin)', 'icon'=>'🏋️'],
                    ] as $opt)
                        <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer hover:border-ng-orange hover:bg-orange-50 transition">
                            <input type="radio" name="activity_level" value="{{ $opt['value'] }}"
                                   {{ old('activity_level') == $opt['value'] ? 'checked' : '' }}
                                   class="text-ng-orange">
                            <span>{{ $opt['icon'] }}</span>
                            <span class="text-sm text-gray-700">{{ $opt['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mt-4 bg-red-50 rounded-xl p-3">
                @foreach($errors->all() as $e)<p class="text-red-600 text-sm">⚠️ {{ $e }}</p>@endforeach
            </div>
        @endif

        <div class="mt-8 flex justify-between">
            <a href="{{ route('onboarding.step', ['step' => 4]) }}" class="btn-outline">← Kembali</a>
            <button type="submit" class="btn-primary">🎉 Mulai NutriGo!</button>
        </div>
    </form>
</div>
@endsection