<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NutriGo - Dashboard Guest</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $guestSummary = $guestSummary ?? null;
    $guestRecommendations = collect($guestRecommendations ?? []);
    $guestInput = $guestInput ?? [];
    $activityOptions = $activityOptions ?? [];
    $provinceOptions = config('nutrigo.provinces');
@endphp

<body class="min-h-screen bg-gradient-to-b from-[#FFF8EE] via-[#F3E8CC] to-[#FFF8EE] text-[#17311f]"
    x-data="{
        authOpen: {{ $errors->any() ? 'true' : 'false' }},
        authTab: {{ !$errors->any() || old('name') ? '\'register\'' : '\'login\'' }}
    }">
    <header class="sticky top-0 z-30 border-b border-black/5 bg-[#f7edd2]/80 backdrop-blur-sm shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/Logo NutriGo 2.png') }}" alt="NutriGo" class="h-11 w-auto">
            </a>
            <nav class="hidden items-center gap-6 text-sm font-semibold text-[#214d2d] md:flex">
                <a href="#program" class="transition hover:text-[#f55c1f]">Dashboard</a>
                <a href="#artikel" class="transition hover:text-[#f55c1f]">Artikel</a>
                <button type="button" @click="authOpen = true; authTab = 'login'"
                    class="rounded-full border border-[#1d5b2f] px-4 py-2 text-[#1d5b2f] transition hover:bg-white">Masuk</button>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <section
            class="rounded-[28px] bg-gradient-to-br from-[#1d5b2f] via-[#195528] to-[#0e3d1f] p-6 text-white shadow-[0_20px_60px_rgba(20,58,31,0.24)] sm:p-8 lg:p-10">
            <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                <div>
                    <span
                        class="inline-flex rounded-full bg-[#f55c1f] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.25em] text-white/95">Nutrition
                        Insight</span>
                    <h1 class="mt-4 max-w-2xl text-3xl font-black leading-tight text-[#fff8ea] sm:text-4xl lg:text-5xl">
                        Menu hari ini sudah disesuaikan dengan kebutuhan kalorimu.
                    </h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-white/80 sm:text-base">
                        Isi data kesehatanmu dan temukan rekomendasi makanan yang sesuai dengan kebutuhan kalori serta
                        wilayahmu.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="#data-kesehatan"
                            class="inline-flex items-center gap-2 rounded-full bg-[#ffc926] px-5 py-3 text-sm font-extrabold text-[#17311f] transition hover:bg-[#ffd953]">
                            Lihat rekomendasi lengkap <span>→</span>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 rounded-[24px] bg-white/8 p-3 backdrop-blur-sm">
                    @php
                        $guestMealCalories = $guestSummary['meal_calories'] ?? [
                            'breakfast' => 0,
                            'lunch' => 0,
                            'dinner' => 0,
                        ];
                    @endphp
                    @foreach ([['label' => 'Sarapan', 'kcal' => $guestMealCalories['breakfast'] ?? 0, 'icon' => '☀️'], ['label' => 'Siang', 'kcal' => $guestMealCalories['lunch'] ?? 0, 'icon' => '🍴'], ['label' => 'Malam', 'kcal' => $guestMealCalories['dinner'] ?? 0, 'icon' => '🌙']] as $item)
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4 text-center shadow-sm">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-white/65">
                                {{ $item['label'] }}</p>
                            <div class="mt-4 text-3xl">{{ $item['icon'] }}</div>
                            <p class="mt-3 text-sm font-bold text-white/90">
                                {{ $item['kcal'] ? number_format($item['kcal']) . ' kcal' : '— kcal' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="data-kesehatan"
            class="rounded-[28px] bg-[#fff6ea] p-5 shadow-[0_14px_40px_rgba(48,31,10,0.09)] ring-1 ring-black/5 lg:p-6">
            <div class="mb-5 flex items-center gap-2 text-sm font-semibold text-[#f55c1f]">
                <span class="h-2.5 w-2.5 rounded-full bg-[#f55c1f]"></span>
                Data Kesehatan
            </div>

            <form id="health-form" method="GET" action="{{ route('user.dashboard') }}#program"
                class="grid gap-4 lg:grid-cols-2">
                <input type="hidden" name="guest_preview" value="1">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#4a4034]">Usia</label>
                    <input id="age" name="age" type="number" min="1" max="120"
                        value="{{ $guestInput['age'] ?? '' }}"
                        class="w-full rounded-2xl border-0 bg-[#fff1d8] px-4 py-3 text-[#17311f] placeholder:text-[#9c8e6f] focus:ring-2 focus:ring-[#f55c1f]"
                        placeholder="Masukkan usia" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#4a4034]">Tinggi Badan (cm)</label>
                    <input id="height_cm" name="height_cm" type="number" min="50" max="300"
                        value="{{ $guestInput['height_cm'] ?? '' }}"
                        class="w-full rounded-2xl border-0 bg-[#fff1d8] px-4 py-3 text-[#17311f] placeholder:text-[#9c8e6f] focus:ring-2 focus:ring-[#f55c1f]"
                        placeholder="Masukkan tinggi badan" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#4a4034]">Berat Badan (kg)</label>
                    <input id="weight_kg" name="weight_kg" type="number" min="10" max="500"
                        value="{{ $guestInput['weight_kg'] ?? '' }}"
                        class="w-full rounded-2xl border-0 bg-[#fff1d8] px-4 py-3 text-[#17311f] placeholder:text-[#9c8e6f] focus:ring-2 focus:ring-[#f55c1f]"
                        placeholder="Masukkan berat badan" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#4a4034]">Aktivitas</label>
                    <select id="activity_level" name="activity_level"
                        class="w-full appearance-none rounded-2xl border border-[#e6d6b0] bg-white px-4 py-3 pr-10 text-[#17311f] shadow-sm hover:border-[#f55c1f] focus:outline-none focus:ring-2 focus:ring-[#f55c1f]"
                        required>
                        <option value="">Pilih aktivitas</option>
                        @foreach ($activityOptions as $a)
                            <option value="{{ $a['value'] }}"
                                {{ ($guestInput['activity_level'] ?? '') === $a['value'] ? 'selected' : '' }}>
                                {{ $a['label'] }} — {{ $a['description'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#4a4034]">Wilayah</label>
                    <select id="province" name="province"
                        class="w-full appearance-none rounded-2xl border border-[#e6d6b0] bg-white px-4 py-3 pr-10 text-[#17311f] shadow-sm hover:border-[#f55c1f] focus:outline-none focus:ring-2 focus:ring-[#f55c1f]"
                        required>
                        <option value="">Pilih wilayah</option>
                        @foreach ($provinceOptions as $province)
                            <option value="{{ $province }}"
                                {{ ($guestInput['province'] ?? '') === $province ? 'selected' : '' }}>
                                {{ $province }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <button type="submit"
                        class="flex w-full items-center justify-center gap-3 rounded-full bg-gradient-to-r from-[#ff7a1f] to-[#f24c16] px-6 py-4 text-sm font-extrabold text-white shadow-[0_16px_30px_rgba(242,76,22,0.28)] transition hover:brightness-105">
                        Hitung BMI <span>→</span>
                    </button>
                </div>
            </form>
        </section>

        @if ($guestSummary)
            @php
                $selectedActivity = collect($activityOptions)->firstWhere('value', $guestSummary['activity_level']);
            @endphp
            <section id="guest-results" class="grid gap-4 md:grid-cols-4">
                <div class="rounded-[22px] bg-[#F96015] p-5 text-white shadow-sm ring-1 ring-black/5">
                    <div
                        class="flex items-center justify-between text-xs font-bold uppercase tracking-[0.2em] text-white/75">
                        <span>BMI Kamu</span><span>i</span>
                    </div>
                    <p class="mt-6 text-4xl font-black">{{ number_format($guestSummary['bmi'], 1) }}</p>
                    <p class="mt-2 text-sm font-semibold text-white/80">{{ $guestSummary['bmi_category'] }}</p>
                </div>
                <div class="rounded-[22px] bg-[#FFC926] p-5 text-[#18542A]">
                    <div
                        class="flex items-center justify-between text-xs font-bold uppercase tracking-[0.2em] text-white/75">
                        <span>Kebutuhan Kalori</span><span>◌</span>
                    </div>
                    <p class="mt-6 text-4xl font-black">{{ number_format($guestSummary['daily_calories']) }}</p>
                    <p class="mt-2 text-sm font-semibold text-white/80">kcal / hari</p>
                </div>
                <div class="rounded-[22px] bg-[#9ABC05] p-5 text-white shadow-sm ring-1 ring-black/5">
                    <div
                        class="flex items-center justify-between text-xs font-bold uppercase tracking-[0.2em] text-white/75">
                        <span>Aktivitas</span><span>⌄</span>
                    </div>
                    <p class="mt-6 text-2xl font-black">{{ $selectedActivity['label'] ?? '—' }}</p>
                    <p class="mt-2 text-sm font-semibold text-white/80">{{ $guestSummary['province'] }}</p>
                </div>
                <div class="rounded-[22px] bg-[#18542A] p-5 text-white shadow-sm ring-1 ring-black/5">
                    <div
                        class="flex items-center justify-between text-xs font-bold uppercase tracking-[0.2em] text-white/75">
                        <span>Alokasi</span><span>⌃</span>
                    </div>
                    <div class="mt-4 space-y-2 text-sm font-semibold text-white/90">
                        <div class="flex items-center justify-between">
                            <span>Sarapan</span><span>{{ number_format($guestSummary['meal_calories']['breakfast']) }}
                                kcal</span>
                        </div>
                        <div class="flex items-center justify-between"><span>Makan
                                Siang</span><span>{{ number_format($guestSummary['meal_calories']['lunch']) }}
                                kcal</span></div>
                        <div class="flex items-center justify-between"><span>Makan
                                Malam</span><span>{{ number_format($guestSummary['meal_calories']['dinner']) }}
                                kcal</span></div>
                    </div>
                </div>
            </section>

            <section
                class="rounded-[28px] bg-[#fff6ea] p-5 shadow-[0_14px_40px_rgba(48,31,10,0.09)] ring-1 ring-black/5 lg:p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-3xl font-black text-[#17311f]">Rekomendasi Makanan</h2>
                        <p class="mt-2 text-sm text-[#726956]">Semua rekomendasi berdasarkan wilayah yang dipilih.</p>
                    </div>
                </div>

                <div class="relative">
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse($guestRecommendations as $food)
                            <article @click="authOpen = true; authTab = 'register'"
                                class="group relative cursor-pointer overflow-hidden rounded-[24px] bg-[#fff4cb] shadow-[0_12px_26px_rgba(56,39,10,0.1)] ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-[0_18px_30px_rgba(56,39,10,0.14)]">
                                <div class="relative h-52 overflow-hidden">
                                    <img src="{{ $food->image ? asset($food->image) : asset('assets/salad-sketsa 1.png') }}"
                                        alt="{{ $food->name }}"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    <div
                                        class="absolute left-3 top-3 rounded-full bg-[#1d5b2f] px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] text-white">
                                        {{ strtoupper($food->origin ?? ($guestSummary['province'] ?? 'Menu')) }}</div>
                                    <div
                                        class="absolute right-3 top-3 rounded-full bg-[#ffc926] px-3 py-1 text-[11px] font-extrabold text-[#17311f]">
                                        {{ number_format($food->calories) }} kcal</div>
                                </div>
                                <div class="space-y-3 p-5">
                                    <div>
                                        <h3 class="text-2xl font-extrabold text-[#245432]">{{ $food->name }}</h3>
                                        <p class="mt-2 text-sm leading-6 text-[#5d5b51]">
                                            {{ $food->composition ?: 'Rekomendasi menu sehat yang bisa disesuaikan setelah data kamu tersimpan.' }}
                                        </p>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button" @click.stop="authOpen = true; authTab = 'register'"
                                            class="flex-1 rounded-full bg-[#da2d1c] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#c32314]">Pilih
                                            Menu</button>
                                        <button type="button" @click.stop="authOpen = true; authTab = 'register'"
                                            class="rounded-full border border-[#1d5b2f] px-4 py-3 text-sm font-bold text-[#1d5b2f] transition hover:bg-white">Detail</button>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div
                                class="rounded-[24px] bg-[#fff4cb] p-6 text-[#245432] shadow-[0_12px_26px_rgba(56,39,10,0.1)] ring-1 ring-black/5 sm:col-span-2 xl:col-span-3">
                                Belum ada rekomendasi untuk wilayah ini.
                            </div>
                        @endforelse
                    </div>
                    <div class="absolute inset-0 z-20 flex items-center justify-center rounded-[24px] bg-black/35 p-6 backdrop-blur-[1.5px]"
                        @click="authOpen = true; authTab = 'register'">
                        <div
                            class="max-w-xl rounded-[22px] border border-white/25 bg-white/12 px-6 py-5 text-center text-white shadow-[0_20px_40px_rgba(0,0,0,0.2)] backdrop-blur-sm">
                            <p class="text-sm font-bold uppercase tracking-[0.2em] text-white/80">Rekomendasi Terkunci
                            </p>
                            <p class="mt-2 text-lg font-black leading-tight">Masuk / Daftar untuk melihat rekomendasi
                                lengkap</p>
                            <button type="button" @click.stop="authOpen = true; authTab = 'register'"
                                class="mt-4 rounded-full bg-[#f55c1f] px-5 py-2.5 text-sm font-extrabold text-white transition hover:brightness-105">Masuk
                                / Daftar</button>
                        </div>
                    </div>
                </div>
            </section>
        @else
<section class="rounded-[28px] bg-[#fff6ea] p-5 shadow-[0_14px_40px_rgba(48,31,10,0.09)] ring-1 ring-black/5 lg:p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-3xl font-black text-[#17311f]">Rekomendasi Makanan</h2>
                        <p class="mt-2 text-sm text-[#726956]">Semua rekomendasi berdasarkan wilayah yang dipilih.</p>
                    </div>
                </div>

                <div class="relative">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse($guestRecommendations as $food)
                        <article @click="authOpen = true; authTab = 'register'" class="group relative cursor-pointer overflow-hidden rounded-[24px] bg-[#fff4cb] shadow-[0_12px_26px_rgba(56,39,10,0.1)] ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-[0_18px_30px_rgba(56,39,10,0.14)]">
                            <div class="relative h-52 overflow-hidden">
                                <img src="{{ $food->image ? asset($food->image) : asset('assets/salad-sketsa 1.png') }}" alt="{{ $food->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                <div class="absolute left-3 top-3 rounded-full bg-[#1d5b2f] px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] text-white">{{ strtoupper($food->origin ?? ($guestSummary['province'] ?? 'Menu')) }}</div>
                                <div class="absolute right-3 top-3 rounded-full bg-[#ffc926] px-3 py-1 text-[11px] font-extrabold text-[#17311f]">{{ number_format($food->calories) }} kcal</div>
                            </div>
                            <div class="space-y-3 p-5">
                                <div>
                                    <h3 class="text-2xl font-extrabold text-[#245432]">{{ $food->name }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-[#5d5b51]">{{ $food->composition ?: 'Rekomendasi menu sehat yang bisa disesuaikan setelah data kamu tersimpan.' }}</p>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" @click.stop="authOpen = true; authTab = 'register'" class="flex-1 rounded-full bg-[#da2d1c] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#c32314]">Pilih Menu</button>
                                    <button type="button" @click.stop="authOpen = true; authTab = 'register'" class="rounded-full border border-[#1d5b2f] px-4 py-3 text-sm font-bold text-[#1d5b2f] transition hover:bg-white">Detail</button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[24px] bg-[#fff4cb] p-6 text-[#245432] shadow-[0_12px_26px_rgba(56,39,10,0.1)] ring-1 ring-black/5 sm:col-span-2 xl:col-span-3">
                            Belum ada rekomendasi untuk wilayah ini.
                        </div>
                    @endforelse
                </div>
                <div class="absolute inset-0 z-20 flex items-center justify-center rounded-[24px] bg-black/35 p-6 backdrop-blur-[1.5px]" @click="authOpen = true; authTab = 'register'">
                    <div class="max-w-xl rounded-[22px] border border-white/25 bg-white/12 px-6 py-5 text-center text-white shadow-[0_20px_40px_rgba(0,0,0,0.2)] backdrop-blur-sm">
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-white/80">Rekomendasi Terkunci</p>
                        <p class="mt-2 text-lg font-black leading-tight">Masuk / Daftar untuk melihat rekomendasi lengkap</p>
                        <button type="button" @click.stop="authOpen = true; authTab = 'register'" class="mt-4 rounded-full bg-[#f55c1f] px-5 py-2.5 text-sm font-extrabold text-white transition hover:brightness-105">Masuk / Daftar</button>
                    </div>
                </div>
                </div>
            </section>
        @endif

        <section id="artikel"
            class="flex flex-col gap-4 rounded-[28px] bg-gradient-to-r from-[#18542A] to-[#236937] p-6 text-white shadow-[0_15px_40px_rgba(24,84,42,0.15)] md:flex-row md:items-center md:justify-between">

            <div>
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-[#FFC926] px-4 py-1 text-xs font-extrabold uppercase tracking-[0.2em] text-[#18542A]">
                    📚 Artikel Terbaru
                </span>

                <h2 class="mt-4 text-3xl font-black lg:text-4xl">
                    Artikel Kesehatan
                </h2>

                <p class="mt-2 max-w-xl text-sm leading-6 text-white/80">
                    Temukan informasi nutrisi, pola makan sehat, serta tips menjaga kesehatan
                    untuk mendukung gaya hidup yang lebih baik setiap hari.
                </p>
            </div>

            <a href="#"
                class="inline-flex items-center justify-center gap-2 rounded-full bg-[#F96015] px-5 py-3 text-sm font-extrabold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:bg-[#D52518]">
                Lihat Semua Artikel
                <span>→</span>
            </a>

        </section>

        <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

            @forelse($articles as $article)
                <article
                    class="group overflow-hidden rounded-[28px] bg-white shadow-[0_15px_35px_rgba(24,84,42,0.08)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_40px_rgba(24,84,42,0.15)]">

                    <div class="relative overflow-hidden">

                        <img src="{{ $article->image ? Storage::url($article->image) : asset('assets/default-article.jpg') }}"
                            alt="{{ $article->title }}"
                            class="h-56 w-full object-cover transition duration-500 group-hover:scale-105">

                        <div class="absolute left-3 top-3">
                            <span
                                class="rounded-full bg-[#FFC926] px-3 py-1 text-[10px] font-extrabold uppercase text-[#18542A]">
                                {{ ucfirst($article->category) }}
                            </span>
                        </div>

                    </div>

                    <div class="p-6">

                        <h3 class="line-clamp-2 text-xl font-black text-[#18542A]">
                            {{ $article->title }}
                        </h3>

                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-[#75684F]">
                            {{ $article->excerpt }}
                        </p>

                        <div class="mt-5 flex items-center justify-between">

                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span>📖 {{ $article->read_time }} menit</span>
                            </div>

                            <div class="flex items-center gap-2">

                                <span class="rounded-full bg-[#F3E8CC] px-3 py-1 text-xs font-bold text-[#18542A]">
                                    {{ $article->author?->name ?? 'Admin' }}
                                </span>

                            </div>

                        </div>

                    </div>

                </article>

            @empty

                <div class="col-span-full rounded-[28px] bg-white p-10 text-center shadow-sm">

                    <div class="text-6xl">📚</div>

                    <h3 class="mt-4 text-xl font-black text-[#18542A]">
                        Belum Ada Artikel
                    </h3>

                    <p class="mt-2 text-[#75684F]">
                        Artikel yang dipublikasikan admin akan muncul di sini.
                    </p>

                </div>
            @endforelse

        </section>
    </main>

    <div x-cloak x-show="authOpen" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @click.self="authOpen = false">
        <div
            class="grid w-full max-w-6xl overflow-hidden rounded-[30px] bg-[#fff8ea] shadow-[0_30px_90px_rgba(0,0,0,0.4)] ring-1 ring-black/5 lg:min-h-[78vh] lg:grid-cols-[0.95fr_1.05fr]">
            <div class="relative hidden overflow-hidden bg-[#163f24] lg:block lg:sticky lg:top-12 lg:min-h-[78vh]">
                <img src="{{ asset('assets/food login 2.jpg') }}" alt="NutriGo login visual"
                    class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(15,65,32,0.2),rgba(15,65,32,0.5))]"></div>
                <div
                    class="absolute left-8 top-8 rounded-full bg-white/15 px-4 py-2 text-xs font-bold uppercase tracking-[0.28em] text-white/90 backdrop-blur-sm">
                    NutriGo</div>
                <div class="absolute bottom-8 left-8 right-8 text-white">
                    <p class="max-w-md text-3xl font-black leading-tight">Daftar untuk melihat rekomendasi makananmu.
                    </p>
                </div>
            </div>

            <div class="relative max-h-[78vh] overflow-y-auto p-6 sm:p-8 lg:p-10">
                <button type="button" @click="authOpen = false"
                    class="absolute right-5 top-5 rounded-full bg-[#fff1d8] px-3 py-1 text-sm font-bold text-[#17311f]">×</button>
                <div class="mx-auto flex max-w-xl flex-col items-center text-center">
                    <img src="{{ asset('assets/Logo NutriGo 2.png') }}" alt="NutriGo" class="h-14 w-auto">
                    <div
                        class="mt-5 inline-flex rounded-full bg-[#fff8d6] px-4 py-2 text-xs font-bold uppercase tracking-[0.28em] text-[#f55c1f]">
                        Masuk / Daftar</div>
                    <h3 class="mt-5 max-w-lg text-3xl font-black leading-tight text-[#17311f] sm:text-4xl">Selamat
                        datang di NutriGo</h3>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-[#7a674e]"
                        x-text="authTab === 'register' ? 'Buat akun untuk melihat rekomendasi makanan yang sesuai dengan kebutuhanmu.' : 'Masuk ke sistem untuk melihat rekomendasi makananmu.'">
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <p class="font-bold">Ada data yang perlu diperbaiki.</p>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6" x-cloak x-show="authTab === 'login'">
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Alamat Email"
                            class="input-field !rounded-full !border-[#1d5b2f] !bg-transparent" required>
                        <input type="password" name="password" placeholder="Kata Sandi"
                            class="input-field !rounded-full !border-[#1d5b2f] !bg-transparent" required>
                        <button type="submit"
                            class="w-full rounded-full bg-gradient-to-r from-[#8dbc00] to-[#1d5b2f] px-5 py-3 text-sm font-extrabold uppercase tracking-[0.2em] text-white">Masuk
                            →</button>
                    </form>
                    <div class="mt-3 text-center text-xs font-semibold text-[#6f6654]">
                        <button type="button" @click="authTab = 'register'" class="text-[#f55c1f]">Belum punya akun?
                            Daftar</button>
                    </div>
                </div>

                <div class="mt-6" x-cloak x-show="authTab === 'register'">
                    <form method="POST" action="{{ route('register') }}" class="space-y-4" id="register-form">
                        @csrf
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap"
                            class="input-field !rounded-full !border-[#1d5b2f] !bg-transparent" required>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Alamat Email"
                            class="input-field !rounded-full !border-[#1d5b2f] !bg-transparent" required>
                        <input type="password" name="password" placeholder="Kata Sandi"
                            class="input-field !rounded-full !border-[#1d5b2f] !bg-transparent" minlength="8"
                            required>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi"
                            class="input-field !rounded-full !border-[#1d5b2f] !bg-transparent" minlength="8"
                            required>

                        <input type="hidden" name="height_cm" id="register-height_cm">
                        <input type="hidden" name="weight_kg" id="register-weight_kg">
                        <input type="hidden" name="age" id="register-age">
                        <input type="hidden" name="activity_level" id="register-activity_level">
                        <input type="hidden" name="province" id="register-province">
                        <input type="hidden" name="bmi" id="register-bmi">
                        <input type="hidden" name="daily_calorie_needs" id="register-daily_calorie_needs">

                        <button type="submit"
                            class="w-full rounded-full bg-gradient-to-r from-[#ff7a1f] to-[#f24c16] px-5 py-3 text-sm font-extrabold uppercase tracking-[0.2em] text-white">Daftar
                            →</button>
                    </form>
                    <p class="mt-3 text-center text-sm text-[#6f6654]">
                        Sudah punya akun?
                        <button type="button" @click="authTab = 'login'"
                            class="font-bold text-[#f55c1f] underline decoration-2 underline-offset-4">Masuk</button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const draftKey = 'nutrigo.healthDraft';
            const guestSummary = @json($guestSummary ?? null);
            const draftFields = ['height_cm', 'weight_kg', 'age', 'activity_level', 'province'];
            const registerDraftFields = ['height_cm', 'weight_kg', 'age', 'activity_level', 'province', 'bmi',
                'daily_calorie_needs'
            ];

            const getDraft = () => {
                try {
                    return JSON.parse(localStorage.getItem(draftKey) || '{}');
                } catch (error) {
                    localStorage.removeItem(draftKey);
                    return {};
                }
            };

            const saveDraft = () => {
                const payload = {
                    height_cm: document.getElementById('height_cm')?.value || '',
                    weight_kg: document.getElementById('weight_kg')?.value || '',
                    age: document.getElementById('age')?.value || '',
                    activity_level: document.getElementById('activity_level')?.value || '',
                    province: document.getElementById('province')?.value || '',
                };

                localStorage.setItem(draftKey, JSON.stringify(payload));
            };

            const syncRegisterDraft = () => {
                const draft = getDraft();
                registerDraftFields.forEach((field) => {
                    const input = document.getElementById(`register-${field}`);
                    if (input) {
                        input.value = draft[field] || '';
                    }
                });

                if (guestSummary) {
                    const bmiInput = document.getElementById('register-bmi');
                    const caloriesInput = document.getElementById('register-daily_calorie_needs');

                    if (bmiInput) bmiInput.value = guestSummary.bmi || '';
                    if (caloriesInput) caloriesInput.value = guestSummary.daily_calories || '';
                }
            };

            draftFields.forEach((field) => {
                const input = document.getElementById(field);
                const stored = getDraft();

                if (input && stored[field]) {
                    input.value = stored[field];
                }

                input?.addEventListener('input', saveDraft);
                input?.addEventListener('change', saveDraft);
            });

            syncRegisterDraft();

            if (guestSummary) {
                const programSection = document.getElementById('program');
                if (programSection) {
                    window.requestAnimationFrame(() => {
                        programSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                }
            }

            const registerForm = document.getElementById('register-form');
            registerForm?.addEventListener('submit', syncRegisterDraft);

            window.syncRegisterDraft = syncRegisterDraft;
        });
    </script>
</body>

</html>
