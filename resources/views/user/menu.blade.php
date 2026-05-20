@extends('layouts.app')
@section('title','Menu')
@section('page-title','Menu Rekomendasi')

@section('content')
<div class="py-4 space-y-6" x-data="menuPage()">

    {{-- ── HEADER KALORI TRACKER ─────────────────────────────── --}}
    <div class="bg-ng-dark-green rounded-2xl p-6 text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-green-300 text-sm">🍽️ Temukan Menu Sehat Untukmu</p>
                <h2 class="text-xl font-bold mt-1">Personalisasi sesuai wilayah & kebutuhanmu</h2>
            </div>
            <div class="flex gap-4 text-center">
                <div>
                    <p class="text-green-300 text-xs">Target</p>
                    <p class="text-2xl font-extrabold text-ng-yellow">{{ number_format($user->daily_calorie_needs ?? 2000) }}</p>
                    <p class="text-green-300 text-xs">kcal</p>
                </div>
                <div class="w-px bg-green-600"></div>
                <div>
                    <p class="text-green-300 text-xs">Dikonsumsi</p>
                    <p class="text-2xl font-extrabold" :class="totalSelected > {{ $user->daily_calorie_needs ?? 2000 }} ? 'text-red-300' : 'text-white'">
                        {{ number_format($totalSelected) }}
                    </p>
                    <p class="text-green-300 text-xs">kcal</p>
                </div>
                <div class="w-px bg-green-600"></div>
                <div>
                    <p class="text-green-300 text-xs">Sisa</p>
                    <p class="text-2xl font-extrabold text-ng-yellow">
                        {{ number_format(max(0, ($user->daily_calorie_needs ?? 2000) - $totalSelected)) }}
                    </p>
                    <p class="text-green-300 text-xs">kcal</p>
                </div>
            </div>
        </div>
        {{-- Progress Bar --}}
        @php
            $pct = min(100, ($totalSelected / max(1, $user->daily_calorie_needs ?? 2000)) * 100);
        @endphp
        <div class="mt-4 bg-green-800 rounded-full h-3">
            <div class="h-3 rounded-full transition-all {{ $pct > 100 ? 'bg-red-400' : 'bg-ng-yellow' }}"
                 style="width: {{ $pct }}%"></div>
        </div>
        <p class="text-green-300 text-xs mt-1">{{ number_format($pct, 1) }}% dari target harian</p>
    </div>

    {{-- ── MENU REKOMENDASI HARI INI ────────────────────────── --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">🌟 Rekomendasi Menu Hari Ini</h3>
                <p class="text-sm text-gray-500">Disusun sesuai kalori & wilayahmu · {{ now()->isoFormat('D MMMM Y') }}</p>
            </div>
            <form method="POST" action="{{ route('user.menu.regenerate') }}">
                @csrf
                <button class="btn-outline text-sm">🔄 Ganti Menu</button>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['label'=>'Sarapan','icon'=>'🌅','food'=>$todayMenu->breakfast,'type'=>'breakfast','pct'=>30],
                ['label'=>'Makan Siang','icon'=>'☀️','food'=>$todayMenu->lunch,'type'=>'lunch','pct'=>40],
                ['label'=>'Makan Malam','icon'=>'🌙','food'=>$todayMenu->dinner,'type'=>'dinner','pct'=>30],
            ] as $meal)
                <div class="border-2 border-gray-100 rounded-2xl p-4 hover:border-ng-orange transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">{{ $meal['icon'] }}</span>
                            <span class="font-semibold text-gray-700 text-sm">{{ $meal['label'] }}</span>
                        </div>
                        <span class="badge-orange text-xs">{{ $meal['pct'] }}%</span>
                    </div>

                    @if($meal['food'])
                        <h4 class="font-bold text-gray-800 mb-1">{{ $meal['food']->name }}</h4>
                        @if($meal['food']->origin)
                            <p class="text-xs text-gray-400 mb-2">📍 {{ $meal['food']->origin }}</p>
                        @endif
                        <div class="text-2xl font-extrabold text-ng-orange mb-2">{{ $meal['food']->calories }} <span class="text-sm font-normal text-gray-500">kcal</span></div>

                        {{-- Makro --}}
                        <div class="grid grid-cols-3 gap-1 mb-3 text-center">
                            <div class="bg-blue-50 rounded-lg p-2">
                                <p class="text-xs text-blue-600 font-semibold">Protein</p>
                                <p class="text-sm font-bold text-blue-700">{{ $meal['food']->proteins }}g</p>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-2">
                                <p class="text-xs text-yellow-600 font-semibold">Karbo</p>
                                <p class="text-sm font-bold text-yellow-700">{{ $meal['food']->carbohydrate }}g</p>
                            </div>
                            <div class="bg-red-50 rounded-lg p-2">
                                <p class="text-xs text-red-600 font-semibold">Lemak</p>
                                <p class="text-sm font-bold text-red-700">{{ $meal['food']->fat }}g</p>
                            </div>
                        </div>

                        @if($meal['food']->composition)
                            <p class="text-xs text-gray-500 mb-3 line-clamp-2">
                                🥬 {{ $meal['food']->composition }}
                            </p>
                        @endif

                        {{-- Cek alergi --}}
                        @php
                            $hasAllergen = false;
                            foreach($allergens as $a) {
                                if(str_contains(strtolower($meal['food']->composition ?? ''), strtolower($a))) {
                                    $hasAllergen = true; break;
                                }
                            }
                        @endphp
                        @if($hasAllergen)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-2 mb-3 text-xs text-red-600">
                                ⚠️ Mengandung bahan yang mungkin menjadi alergenmu
                            </div>
                        @endif

                        <form method="POST" action="{{ route('user.menu.log') }}">
                            @csrf
                            <input type="hidden" name="food_id" value="{{ $meal['food']->id }}">
                            <input type="hidden" name="meal_type" value="{{ $meal['type'] }}">
                            <button class="btn-primary w-full text-sm py-2">
                                ✅ Log Makan Ini
                            </button>
                        </form>
                    @else
                        <div class="text-center py-6 text-gray-400">
                            <p class="text-3xl mb-2">🍽️</p>
                            <p class="text-sm">Menu tidak tersedia</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── FILTER CEPAT ─────────────────────────────────────── --}}
    <div class="card">
        <h3 class="font-bold text-gray-800 mb-4">🔍 Jelajahi Menu Nusantara</h3>
        <form method="GET" action="{{ route('user.menu') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="text-xs font-semibold text-gray-600 block mb-1">Filter Wilayah</label>
                <select name="province" class="input-field text-sm">
                    <option value="">Semua Wilayah</option>
                    @foreach(config('nutrigo.provinces') as $prov)
                        <option value="{{ $prov }}" {{ $province == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-36">
                <label class="text-xs font-semibold text-gray-600 block mb-1">Tipe Makan</label>
                <select name="meal_type" class="input-field text-sm">
                    <option value="">Semua</option>
                    <option value="breakfast" {{ request('meal_type')=='breakfast'?'selected':'' }}>Sarapan</option>
                    <option value="lunch"     {{ request('meal_type')=='lunch'?'selected':'' }}>Makan Siang</option>
                    <option value="dinner"    {{ request('meal_type')=='dinner'?'selected':'' }}>Makan Malam</option>
                    <option value="snack"     {{ request('meal_type')=='snack'?'selected':'' }}>Snack</option>
                </select>
            </div>
            <div class="flex-1 min-w-36">
                <label class="text-xs font-semibold text-gray-600 block mb-1">Kalori Maks</label>
                <select name="max_cal" class="input-field text-sm">
                    <option value="">Semua</option>
                    <option value="200"  {{ request('max_cal')=='200'?'selected':'' }}>&lt; 200 kcal</option>
                    <option value="400"  {{ request('max_cal')=='400'?'selected':'' }}>&lt; 400 kcal</option>
                    <option value="600"  {{ request('max_cal')=='600'?'selected':'' }}>&lt; 600 kcal</option>
                    <option value="800"  {{ request('max_cal')=='800'?'selected':'' }}>&lt; 800 kcal</option>
                </select>
            </div>
            <div>
                <button class="btn-primary text-sm">Filter</button>
                <a href="{{ route('user.menu') }}" class="btn-outline text-sm ml-2">Reset</a>
            </div>
        </form>

        {{-- Alergi aktif --}}
        @if(count($allergens) > 0)
            <div class="mt-3 flex flex-wrap gap-2 items-center">
                <span class="text-xs text-gray-500">Alergi aktif:</span>
                @foreach($allergens as $a)
                    <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-medium">🚫 {{ $a }}</span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── DAFTAR MAKANAN ──────────────────────────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">
                Semua Menu
                @if(request('province'))
                    <span class="text-ng-orange">· {{ request('province') }}</span>
                @endif
            </h3>
            <span class="text-sm text-gray-500">{{ $foods->total() }} makanan</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($foods as $food)
                @php
                    $safe = true;
                    foreach($allergens as $a) {
                        if(str_contains(strtolower($food->composition ?? ''), strtolower($a))) { $safe = false; break; }
                    }
                @endphp
                <div class="card hover:shadow-md transition-all relative {{ !$safe ? 'opacity-75' : '' }}"
                     x-data="{ expanded: false }">
                    {{-- Badge alergi --}}
                    @if(!$safe)
                        <span class="absolute top-3 right-3 bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-medium">⚠️ Alergen</span>
                    @endif

                    {{-- Meal type badge --}}
                    <div class="flex items-center gap-2 mb-3">
                        <span class="{{ match($food->meal_type) {
                            'breakfast' => 'badge-orange',
                            'lunch'     => 'badge-green',
                            'dinner'    => 'bg-purple-100 text-purple-700 text-xs px-2.5 py-1 rounded-full font-medium',
                            default     => 'bg-gray-100 text-gray-700 text-xs px-2.5 py-1 rounded-full font-medium',
                        } }}">
                            {{ match($food->meal_type) {
                                'breakfast' => '🌅 Sarapan',
                                'lunch'     => '☀️ Siang',
                                'dinner'    => '🌙 Malam',
                                default     => '🍪 Snack',
                            } }}
                        </span>
                    </div>

                    <h4 class="font-bold text-gray-800 mb-1">{{ $food->name }}</h4>

                    @if($food->origin)
                        <p class="text-xs text-gray-400 mb-2">📍 {{ $food->origin }}</p>
                    @endif

                    <div class="text-2xl font-extrabold text-ng-orange mb-3">
                        {{ $food->calories }} <span class="text-sm font-normal text-gray-500">kcal</span>
                    </div>

                    {{-- Makro mini --}}
                    <div class="flex gap-3 text-xs text-gray-600 mb-3">
                        <span>🥩 {{ $food->proteins }}g protein</span>
                        <span>🌾 {{ $food->carbohydrate }}g karbo</span>
                        <span>🧈 {{ $food->fat }}g lemak</span>
                    </div>

                    {{-- Expandable composition --}}
                    @if($food->composition)
                        <div x-show="!expanded" class="text-xs text-gray-500 line-clamp-2 mb-2">{{ $food->composition }}</div>
                        <div x-show="expanded" x-transition class="text-xs text-gray-500 mb-2">{{ $food->composition }}</div>
                        <button @click="expanded = !expanded" class="text-xs text-ng-orange hover:underline mb-3">
                            <span x-text="expanded ? 'Sembunyikan ↑' : 'Lihat komposisi ↓'"></span>
                        </button>
                    @endif

                    {{-- Log button --}}
                    <div x-data="{ showLog: false }">
                        <button @click="showLog = !showLog" class="btn-primary w-full text-sm py-2">
                            + Log Makan
                        </button>
                        <div x-show="showLog" x-transition class="mt-3 space-y-2">
                            @foreach(['breakfast'=>'🌅 Sarapan','lunch'=>'☀️ Makan Siang','dinner'=>'🌙 Makan Malam','snack'=>'🍪 Snack'] as $type => $typeLabel)
                                <form method="POST" action="{{ route('user.menu.log') }}">
                                    @csrf
                                    <input type="hidden" name="food_id" value="{{ $food->id }}">
                                    <input type="hidden" name="meal_type" value="{{ $type }}">
                                    <button class="w-full text-left text-sm py-2 px-3 bg-gray-50 hover:bg-orange-50 hover:text-ng-orange rounded-lg transition">
                                        {{ $typeLabel }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-gray-400">
                    <p class="text-4xl mb-3">🍽️</p>
                    <p class="font-semibold">Tidak ada menu ditemukan</p>
                    <p class="text-sm">Coba ubah filter atau reset pencarian</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">{{ $foods->withQueryString()->links() }}</div>
    </div>

</div>

<script>
function menuPage() {
    return {
        totalSelected: {{ $totalSelected }}
    }
}
</script>
@endsection