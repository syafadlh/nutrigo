@extends('layouts.admin')
@section('title','Kelola User')
@section('page-title','Kelola User')

@section('content')
<div class="mb-6">
    <form method="GET" class="flex gap-3 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama atau email..." class="input-field w-72 text-sm">
        <button class="btn-primary text-sm">Cari</button>
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">User</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Wilayah</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Data Fisik</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Kalori/Hari</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-ng-yellow flex items-center justify-center font-bold text-sm text-gray-800">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $u->name }}</p>
                                <p class="text-xs text-gray-400">{{ $u->email }}</p>
                                @if($u->nickname)
                                    <p class="text-xs text-gray-500">Dipanggil: <span class="font-medium">{{ $u->nickname }}</span></p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600">
                        @if($u->province)
                            <p class="text-sm">{{ $u->city }}</p>
                            <p class="text-xs text-gray-400">{{ $u->province }}</p>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-600">
                        @if($u->height_cm)
                            <p class="text-sm">{{ $u->height_cm }}cm / {{ $u->weight_kg }}kg</p>
                            <p class="text-xs">BMI: <span class="font-semibold {{ $u->bmi >= 25 ? 'text-red-500' : 'text-green-600' }}">{{ $u->bmi }}</span></p>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($u->daily_calorie_needs)
                            <span class="font-bold text-ng-orange">{{ number_format($u->daily_calorie_needs) }} kcal</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($u->onboarding_completed)
                            <span class="badge-green">✅ Aktif</span>
                        @else
                            <span class="badge-orange">⏳ Setup</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-3">
                            <a href="{{ route('admin.users.show', $u) }}" class="text-blue-500 hover:underline text-xs font-medium">Detail</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                onsubmit="return confirm('Yakin hapus user {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-12 text-gray-400">Tidak ada user ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4 border-t">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection