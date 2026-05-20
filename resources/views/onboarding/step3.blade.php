@extends('onboarding.layout')
@php $currentStep = 3; @endphp

@section('content')
<div class="bg-white rounded-3xl shadow-xl p-8" x-data="{ hasAllergy: '{{ old('has_allergy', 'no') }}' }">
    <div class="mb-8">
        <span class="text-5xl">🚨</span>
        <h2 class="text-2xl font-bold text-gray-800 mt-3">Alergi Makanan</h2>
        <p class="text-gray-500 mt-1 text-sm">Ini penting supaya rekomendasimu aman!</p>
    </div>

    <form method="POST" action="{{ route('onboarding.save.3') }}">
        @csrf

        <div class="mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-3">Apakah kamu memiliki alergi makanan?</p>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer"
                       :class="hasAllergy==='yes' ? 'border-ng-orange bg-orange-50' : 'border-gray-200'">
                    <input type="radio" name="has_allergy" value="yes" x-model="hasAllergy" class="hidden">
                    <span class="text-2xl">⚠️</span>
                    <span class="font-semibold text-gray-700">Ya, ada</span>
                </label>
                <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer"
                       :class="hasAllergy==='no' ? 'border-ng-green bg-green-50' : 'border-gray-200'">
                    <input type="radio" name="has_allergy" value="no" x-model="hasAllergy" class="hidden">
                    <span class="text-2xl">✅</span>
                    <span class="font-semibold text-gray-700">Tidak ada</span>
                </label>
            </div>
        </div>

        <div x-show="hasAllergy==='yes'" x-transition>
            <p class="text-sm font-semibold text-gray-700 mb-3">Pilih alergenmu:</p>
            <div class="grid grid-cols-3 gap-2 mb-4">
                @foreach(['Seafood','Udang','Kepiting','Cumi','Kacang','Susu','Telur','Gluten','Kedelai','Gandum'] as $allergen)
                    <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:border-ng-orange hover:bg-orange-50 transition">
                        <input type="checkbox" name="allergens[]" value="{{ strtolower($allergen) }}" class="rounded text-ng-orange">
                        <span class="text-sm text-gray-700">{{ $allergen }}</span>
                    </label>
                @endforeach
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-2">Alergi lainnya (opsional):</label>
                <input type="text" name="custom_allergy" class="input-field" placeholder="Cth: durian, pete...">
            </div>
        </div>

        <div class="mt-8 flex justify-between">
            <a href="{{ route('onboarding.step', ['step' => 2]) }}" class="btn-outline">← Kembali</a>
            <button type="submit" class="btn-primary">Selanjutnya →</button>
        </div>
    </form>
</div>
@endsection