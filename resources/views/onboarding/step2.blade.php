@extends('onboarding.layout')
@php $currentStep = 2; @endphp

@section('content')
<div class="bg-white rounded-3xl shadow-xl p-8">
    <div class="mb-8">
        <div class="bg-ng-cream rounded-2xl p-5 mb-6">
            <p class="font-bold text-ng-dark-green text-lg mb-2">🗺️ NutriGo & Menu Nusantara</p>
            <p class="text-gray-600 text-sm leading-relaxed">
                NutriGo adalah platform pemilihan makanan berbasis kalori harian, wilayah, dan kondisi kesehatanmu.
                Kami <strong>bukan program diet</strong> — kami membantu kamu makan dengan cerdas dan menikmati
                kekayaan kuliner nusantara sesuai kebutuhanmu. 🌿
            </p>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Kamu dari mana? 📍</h2>
        <p class="text-gray-500 mt-1 text-sm">Kami akan merekomendasikan menu lokal dari daerahmu</p>
    </div>

    <form method="POST" action="{{ route('onboarding.save.2') }}">
        @csrf
        <div class="space-y-5">
            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-2">Provinsi</label>
                <select name="province" class="input-field" required>
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach(config('nutrigo.provinces') as $prov)
                        <option value="{{ $prov }}" {{ old('province', $user->province) == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700 block mb-2">Kota / Kabupaten</label>
                <input type="text" name="city" value="{{ old('city', $user->city) }}"
                       class="input-field" placeholder="Cth: Surabaya, Bandung, Makassar" required>
            </div>
        </div>

        @if($errors->any())
            <div class="mt-4 bg-red-50 rounded-xl p-3">
                @foreach($errors->all() as $e)<p class="text-red-600 text-sm">⚠️ {{ $e }}</p>@endforeach
            </div>
        @endif

        <div class="mt-8 flex justify-between">
            <a href="{{ route('onboarding.step', ['step' => 1]) }}" class="btn-outline">← Kembali</a>
            <button type="submit" class="btn-primary">Selanjutnya →</button>
        </div>
    </form>
</div>
@endsection