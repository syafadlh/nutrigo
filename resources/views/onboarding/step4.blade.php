@extends('onboarding.layout')
@php $currentStep = 4; @endphp

@section('content')
<div class="bg-white rounded-3xl shadow-xl p-8" x-data="{ hasMedical: '{{ old('has_medical_need', 'no') }}' }">
    <div class="mb-8">
        <span class="text-5xl">💊</span>
        <h2 class="text-2xl font-bold text-gray-800 mt-3">Kebutuhan Khusus</h2>
        <p class="text-gray-500 mt-1 text-sm">
            Apakah kamu memiliki kebutuhan makanan khusus dari medis atau kebiasaan pribadi?
            <em class="block text-xs mt-1">(Contoh: harus konsumsi telur 2-3 butir/hari, minum susu setiap malam)</em>
        </p>
    </div>

    <form method="POST" action="{{ route('onboarding.save.4') }}">
        @csrf
        <input type="hidden" name="has_medical_need" :value="hasMedical">

        <div class="grid grid-cols-2 gap-3 mb-6">
            <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer"
                   :class="hasMedical==='yes' ? 'border-ng-orange bg-orange-50' : 'border-gray-200'">
                <input type="radio" value="yes" x-model="hasMedical" class="hidden">
                <span class="text-2xl">✍️</span>
                <span class="font-semibold text-gray-700">Ya, ada</span>
            </label>
            <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer"
                   :class="hasMedical==='no' ? 'border-ng-green bg-green-50' : 'border-gray-200'">
                <input type="radio" value="no" x-model="hasMedical" class="hidden">
                <span class="text-2xl">😊</span>
                <span class="font-semibold text-gray-700">Tidak ada</span>
            </label>
        </div>

        <div x-show="hasMedical==='yes'" x-transition class="space-y-4">
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="text-sm font-medium text-gray-700 block mb-1">Makanan/Bahan</label>
                    <input type="text" name="food_item" class="input-field" placeholder="Cth: telur, susu, wortel">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Jumlah</label>
                    <input type="number" name="quantity" class="input-field" placeholder="2" min="1">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Satuan</label>
                    <select name="unit" class="input-field">
                        <option value="butir">butir</option>
                        <option value="porsi">porsi</option>
                        <option value="gram">gram</option>
                        <option value="ml">ml</option>
                        <option value="gelas">gelas</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Durasi</label>
                    <select name="duration_type" class="input-field">
                        <option value="daily">Setiap hari</option>
                        <option value="weekly">Seminggu sekali</option>
                        <option value="yearly">Setahun</option>
                        <option value="forever">Seterusnya</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-between">
            <a href="{{ route('onboarding.step', ['step' => 3]) }}" class="btn-outline">← Kembali</a>
            <button type="submit" class="btn-primary">Selanjutnya →</button>
        </div>
    </form>
</div>
@endsection