@extends('layouts.admin')
@section('title','Tambah Makanan')
@section('page-title','Tambah Data Makanan')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.foods.store') }}">
        @csrf
        <div class="card space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Nama Makanan *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input-field" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Kalori (kcal) *</label>
                    <input type="number" name="calories" value="{{ old('calories') }}" class="input-field" step="0.1" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Tipe Makan *</label>
                    <select name="meal_type" class="input-field" required>
                        <option value="breakfast">Sarapan</option>
                        <option value="lunch">Makan Siang</option>
                        <option value="dinner">Makan Malam</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Protein (g)</label>
                    <input type="number" name="proteins" value="{{ old('proteins', 0) }}" class="input-field" step="0.1">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Karbohidrat (g)</label>
                    <input type="number" name="carbohydrate" value="{{ old('carbohydrate', 0) }}" class="input-field" step="0.1">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Lemak (g)</label>
                    <input type="number" name="fat" value="{{ old('fat', 0) }}" class="input-field" step="0.1">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Asal Daerah</label>
                    <select name="origin" class="input-field">
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach(config('nutrigo.provinces') as $prov)
                            <option value="{{ $prov }}" {{ old('origin') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Komposisi / Bahan</label>
                    <textarea name="composition" rows="3" class="input-field resize-none"
                            placeholder="Cth: nasi, ayam, santan, bumbu kuning...">{{ old('composition') }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 justify-end pt-2 border-t border-gray-100">
                <a href="{{ route('admin.foods.index') }}" class="btn-outline">Batal</a>
                <button class="btn-primary">+ Tambah Makanan</button>
            </div>
        </div>
    </form>
</div>
@endsection