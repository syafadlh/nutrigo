@extends('layouts.app')
@section('title','Menu')
@section('page-title','Menu Pengganti')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    {{-- ── HEADER KALORI TRACKER (KEEP EXISTING DESIGN) ───────────────── --}}
    <div class="bg-ng-dark-green rounded-2xl p-6 text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-green-300 text-sm">🧩 Pilih menu pengganti untukmu</p>
                <h2 class="text-xl font-bold mt-1">Disusun sesuai kalori & wilayahmu</h2>
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

        @php
            $pct = min(100, ($totalSelected / max(1, $user->daily_calorie_needs ?? 2000)) * 100);
        @endphp
        <div class="mt-4 bg-green-800 rounded-full h-3">
            <div
                class="h-3 rounded-full transition-all {{ $pct > 100 ? 'bg-red-400' : 'bg-ng-yellow' }}"
                style="width: {{ $pct }}%">
            </div>
        </div>
        <p class="text-green-300 text-xs mt-1">{{ number_format($pct, 1) }}% dari target harian</p>
    </div>

    <div class="mb-4">
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 text-[#18542D] font-bold hover:opacity-90">
            <span aria-hidden>←</span> Kembali
        </a>
    </div>

    {{-- ── PAGE CONTEXT ─────────────────────────────────────────────── --}}

    @php
        $slotMealType = request('meal_type'); // breakfast | lunch | dinner (from Reminder -> Ganti Menu)
        $slotLabel = match($slotMealType) {
            'breakfast' => 'Sarapan',
            'lunch' => 'Makan Siang',
            'dinner' => 'Makan Malam',
            default => 'Pengganti',
        };

        $regionChips = [
            '' => 'Semua',
            'Banten' => 'Banten',
            'DKI Jakarta' => 'DKI Jakarta',
            'Jawa Barat' => 'Jawa Barat',
            'Jawa Tengah' => 'Jawa Tengah',
            'DI Yogyakarta' => 'DI Yogyakarta',
            'Jawa Timur' => 'Jawa Timur',
        ];

        $selectedProvince = request('province', '');

        // Group foods by origin region label; fallback to first chip that matches.
        $allFoods = $foods ?? collect();
        $foodsByRegion = collect([
            'Banten' => collect(),
            'DKI Jakarta' => collect(),
            'Jawa Barat' => collect(),
            'Jawa Tengah' => collect(),
            'DI Yogyakarta' => collect(),
            'Jawa Timur' => collect(),
        ]);

        $safeAllergens = $allergens ?? [];

        foreach ($allFoods as $food) {
            $origin = $food->origin ?? '';
            $regionKey = null;

            foreach (array_keys($foodsByRegion->all()) as $key) {
                if (stripos($origin, $key) !== false) {
                    $regionKey = $key;
                    break;
                }
            }

            // If food has no origin match, treat as 'Semua' bucket by skipping grouping unless user chose no province.
            if ($selectedProvince && $regionKey === null) {
                continue;
            }

            $regionKey = $regionKey ?? 'Banten'; // deterministic fallback bucket (will be overridden if chips narrow)

            // Skip allergens defensively (UI-only safety). Backend already filters for allergies in recommendation.
            $composition = strtolower($food->composition ?? '');
            $isSafe = true;
            foreach ($safeAllergens as $a) {
                if ($a && str_contains($composition, strtolower($a))) {
                    $isSafe = false;
                    break;
                }
            }
            if (!$isSafe) continue;

            if ($foodsByRegion->has($regionKey)) {
                $foodsByRegion[$regionKey]->push($food);
            }
        }

        // If user selected a province chip (not "Semua"), keep only that region.
        // NOTE: "$selectedProvince" becomes '' when "Semua" is active.
        if ($selectedProvince !== '' && $selectedProvince) {
            foreach ($foodsByRegion->keys()->all() as $k) {
                if ($k !== $selectedProvince) {
                    $foodsByRegion[$k] = collect();
                }
            }
        }


        // Clean empty regions list for rendering order
        $orderedRegions = ['Jawa Timur','Jawa Tengah','DI Yogyakarta','DKI Jakarta','Jawa Barat','Banten'];
    @endphp

    {{-- ── Search + Region Chips (Flow-B only) ───────────────────────── --}}
    <section class="rounded-[28px] bg-[#F3E8CC] border border-[#E8DBB8] p-6 shadow-[0_12px_26px_rgba(161,114,0,0.16)]">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-black text-[#17311f]">Pilih menu pengganti · {{ $slotLabel }}</h3>
                <p class="mt-2 text-sm text-[#4d5a4f]">Klik <strong>Pilih Menu</strong> untuk mengganti slot pada rencana makanmu.</p>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Cari makanan</label>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    class="input-field text-sm w-full"
                    placeholder="Contoh: rawon, soto, pecel..."
                    oninput="this.form.submit()"
                >
            </div>
            <div>
                {{-- keep query params in same request form --}}
                <form method="GET" action="{{ route('user.menu') }}" class="hidden"></form>
            </div>
        </div>

        <form method="GET" action="{{ route('user.menu') }}" class="mt-4">
            {{-- preserve slot so we update correct *_id --}}
            @if($slotMealType)
                <input type="hidden" name="meal_type" value="{{ $slotMealType }}">
            @endif
            {{-- preserve search query --}}
            @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <div class="flex flex-wrap gap-2">
                @foreach($regionChips as $prov => $label)
                    @php
                        $isActive = ($selectedProvince === $prov);
                        $params = array_filter([
                            'meal_type' => $slotMealType,
                            'province' => $prov,
                            'q' => request('q'),
                        ]);
                    @endphp
                    <a
                        href="{{ route('user.menu', $params) }}"
                        class="px-3 py-2 rounded-full text-xs font-bold border transition {{ $isActive ? 'bg-ng-orange text-white border-ng-orange' : 'bg-white text-gray-700 border-gray-200 hover:border-ng-orange hover:text-ng-orange' }}"
                    >{{ $label }}</a>
                @endforeach
            </div>
        </form>
    </section>

    {{-- ── Foods grouped by region ─────────────────────────────── --}}
    <section class="space-y-6">
        @foreach($orderedRegions as $region)
            @php
                $bucket = $foodsByRegion[$region] ?? collect();
            @endphp
            @continue($bucket->isEmpty())

            <div class="rounded-[28px] bg-[#fff4cb] border border-[#ece1c1] p-6">
                <h4 class="text-lg font-black text-[#18542D] mb-4">{{ $region }}</h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($bucket as $food)
                        <article class="rounded-2xl bg-white/70 ring-1 ring-black/5 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-black text-[#18542D]">{{ $food->name }}</p>
                                    @if($food->origin)
                                        <p class="text-xs text-gray-500 mt-1">📍 {{ $food->origin }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-ng-orange">{{ $food->calories }} <span class="text-xs font-normal text-gray-500">kcal</span></div>
                                </div>
                            </div>

                            <div class="mt-3 flex gap-3 text-xs text-gray-700">
                                <span class="bg-blue-50 rounded-lg px-2 py-1">💪 {{ $food->proteins }}g</span>
                                <span class="bg-yellow-50 rounded-lg px-2 py-1">🌾 {{ $food->carbohydrate }}g</span>
                            </div>

                            <div class="mt-4">
                                <form method="POST" action="{{ route('user.menu.select') }}" class="pick-menu-form">
                                    @csrf
                                    <input type="hidden" name="food_id" value="{{ $food->id }}">
                                    <input type="hidden" name="meal_type" value="{{ $slotMealType }}">
                                    <input type="hidden" name="_redirect_to" value="{{ route('user.dashboard') }}">
                                    <button
                                        type="submit"
                                        class="w-full rounded-full bg-[#18542D] px-6 py-3 text-sm font-bold text-white shadow-[0_8px_20px_rgba(24,91,45,0.18)] transition hover:bg-[#2B7A43]"
                                    >
                                        Pilih Menu
                                    </button>
                                </form>
                            </div>

                        </article>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if(($foods ?? collect())->isEmpty())
            <div class="rounded-[28px] bg-white/60 border border-[#ece1c1] p-10 text-center text-gray-500">
                <p class="text-4xl mb-2">🍽️</p>
                <p class="font-semibold">Tidak ada menu ditemukan</p>
                <p class="text-sm mt-1">Coba ubah filter wilayah atau kata kunci pencarian.</p>
            </div>
        @endif
    </section>

</div>

<script>
function menuPage() {
    return {
        totalSelected: {{ $totalSelected }}
    }
}

// Prevent raw JSON page from flashing: submit via AJAX, then redirect.
document.addEventListener('submit', async function (e) {
    const form = e.target && e.target.closest ? e.target.closest('.pick-menu-form') : null;
    if (!form) return;

    e.preventDefault();

    const url = form.action;
    const fd = new FormData(form);

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: fd,
        });

        const data = await res.json().catch(() => ({}));

            if (res.ok) {
                // show success toast/modal (dashboard provides showSuccessModal)
                if (typeof showSuccessModal === 'function') {
                    showSuccessModal(data.message || 'Menu berhasil dipilih', null);
                }

                const redirectTo = form.querySelector('input[name="_redirect_to"]')?.value || '{{ route('user.dashboard') }}';
                setTimeout(() => window.location.href = redirectTo, 900);
            } else {
                if (typeof showSuccessModal === 'function') {
                    showSuccessModal(data.message || 'Gagal memilih menu');
                }
            }
    } catch (err) {
        if (typeof showSuccessModal === 'function') {
            showSuccessModal('Terjadi kesalahan.');
        } else {
            alert('Terjadi kesalahan.');
        }
    }
});
</script>
@endsection


