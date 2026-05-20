    @extends('layouts.admin')
    @section('title','Data Makanan')
    @section('page-title','Data Makanan')

    @section('content')
    <div class="flex justify-between items-center mb-6">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari makanan..." class="input-field w-64">
            <select name="meal_type" class="input-field w-40">
                <option value="">Semua Tipe</option>
                @foreach(['breakfast'=>'Sarapan','lunch'=>'Makan Siang','dinner'=>'Makan Malam','snack'=>'Snack'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('meal_type')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button class="btn-primary">Filter</button>
        </form>
        <a href="{{ route('admin.foods.create') }}" class="btn-primary">+ Tambah Makanan</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600">Nama</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600">Kalori</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600">Protein</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600">Tipe</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600">Asal</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($foods as $food)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $food->name }}</td>
                        <td class="px-5 py-3 text-ng-orange font-semibold">{{ $food->calories }} kcal</td>
                        <td class="px-5 py-3 text-gray-600">{{ $food->proteins }}g</td>
                        <td class="px-5 py-3">
                            <span class="badge-green">{{ ucfirst($food->meal_type) }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $food->origin ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="{{ $food->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $food->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.foods.edit', $food) }}" class="text-blue-500 hover:underline text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.foods.destroy', $food) }}"
                                    onsubmit="return confirm('Hapus makanan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline text-xs font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-4 border-t">{{ $foods->withQueryString()->links() }}</div>
    </div>
    @endsection