@extends('layouts.admin')
@section('title','Edit Makanan')
@section('page-title','Edit Data Makanan')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.foods.update', $food) }}">
        @csrf @method('PUT')
        <div class="card space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Nama Makanan *</label>
                    <input type="text" name="name" value="{{ old('name', $food->name) }}" class="input-field" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Kalori (kcal) *</label>
                    <input type="number" name="calories" value="{{ old('calories', $food->calories) }}" class="input-field" step="0.1" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Tipe Makan *</label>
                    <select name="meal_type" class="input-field" required>
                        @foreach(['breakfast'=>'Sarapan','lunch'=>'Makan Siang','dinner'=>'Makan Malam','snack'=>'Snack'] as $v => $l)
                            <option value="{{ $v }}" {{ old('meal_type', $food->meal_type) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Protein (g)</label>
                    <input type="number" name="proteins" value="{{ old('proteins', $food->proteins) }}" class="input-field" step="0.1">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Karbohidrat (g)</label>
                    <input type="number" name="carbohydrate" value="{{ old('carbohydrate', $food->carbohydrate) }}" class="input-field" step="0.1">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Lemak (g)</label>
                    <input type="number" name="fat" value="{{ old('fat', $food->fat) }}" class="input-field" step="0.1">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Asal Daerah</label>
                    <select name="origin" class="input-field">
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach(config('nutrigo.provinces') as $prov)
                            <option value="{{ $prov }}" {{ old('origin', $food->origin) == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="flex items-center gap-2 cursor-pointer mt-5">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $food->is_active) ? 'checked' : '' }}
                            class="rounded w-5 h-5 text-ng-green">
                        <span class="text-sm font-semibold text-gray-700">Makanan Aktif</span>
                    </label>
                </div>
                <div class="col-span-2">
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Komposisi / Bahan</label>
                    <textarea name="composition" rows="3" class="input-field resize-none">{{ old('composition', $food->composition) }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 justify-end pt-2 border-t border-gray-100">
                <a href="{{ route('admin.foods.index') }}" class="btn-outline">Batal</a>
                <button class="btn-primary">💾 Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection