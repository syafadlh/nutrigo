<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriGo - Temukan Menu Sehat Harianmu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-ng-cream min-h-screen" x-data>

    {{-- NAVBAR --}}
    <nav class="bg-white/80 backdrop-blur-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <span class="text-2xl font-extrabold text-gray-800">nutri<span class="text-ng-orange">Go</span></span>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-ng-orange font-medium text-sm transition">Masuk</a>
                <a href="{{ route('register') }}" class="btn-primary text-sm">Daftar Gratis</a>
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="max-w-6xl mx-auto px-6 pt-20 pb-16 text-center">
        <span class="bg-ng-orange/10 text-ng-orange text-xs font-semibold px-4 py-1.5 rounded-full">🌟 Menu Nusantara · Berbasis Kalori · Bukan Diet</span>
        <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 mt-6 leading-tight">
            Sehat Itu <span class="text-ng-orange">Seru</span><br>& Nikmat!
        </h1>
        <p class="text-gray-600 text-lg mt-4 max-w-xl mx-auto leading-relaxed">
            Tracking kalori dan rekomendasi menu nusantara yang pas buat lidah Gen Z.
            Bukan program diet — tapi gaya hidup sehat yang menyenangkan.
        </p>
        <div class="flex gap-4 justify-center mt-8">
            <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-3">Mulai Sekarang 🚀</a>
            <a href="#fitur" class="btn-outline text-base px-8 py-3">Pelajari Lebih Lanjut</a>
        </div>
    </section>

    {{-- FITUR --}}
    <section id="fitur" class="max-w-6xl mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-3">Fitur Gokil Buat Kamu</h2>
        <p class="text-center text-gray-500 mb-12">Semua yang kamu butuhkan untuk makan lebih cerdas</p>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['icon'=>'🔥','title'=>'Hitung Kalori Otomatis','desc'=>'Hitung kebutuhan kalori harianmu secara otomatis berdasarkan tinggi, berat, usia & aktivitas','color'=>'bg-ng-yellow'],
                ['icon'=>'🔔','title'=>'Notifikasi & Reminder','desc'=>'Dapatkan pengingat waktu makan agar pola makanmu tetap teratur setiap hari','color'=>'bg-ng-orange'],
                ['icon'=>'🍽️','title'=>'Rekomendasi Menu Harian','desc'=>'Menu sarapan, makan siang, dan malam yang sesuai dengan kebutuhan nutrisimu','color'=>'bg-ng-orange'],
                ['icon'=>'🚫','title'=>'Alergi Filter','desc'=>'Hindari makanan yang tidak cocok dengan kondisi tubuhmu secara otomatis','color'=>'bg-white'],
                ['icon'=>'🗺️','title'=>'Menu Nusantara','desc'=>'Ribuan resep sehat asli Indonesia di ujung jarimu, dipilih sesuai wilayahmu','color'=>'bg-ng-green'],
                ['icon'=>'🔄','title'=>'Variasi Menu Otomatis','desc'=>'Menu yang bervariasi setiap hari berdasarkan riwayat makanmu — anti bosan!','color'=>'bg-ng-orange'],
            ] as $f)
                <div class="card hover:shadow-md transition-all hover:-translate-y-1">
                    <div class="w-10 h-10 {{ $f['color'] }} rounded-xl flex items-center justify-center text-xl mb-4">{{ $f['icon'] }}</div>
                    <h3 class="font-bold text-gray-800 mb-2">{{ $f['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CARA KERJA --}}
    <section class="bg-white py-16">
        <div class="max-w-4xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Cara Mainnya Gampang!</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach([
                    ['step'=>'1','title'=>'Input Data','desc'=>'Tulis tinggi, berat, dan goal kamu'],
                    ['step'=>'2','title'=>'Hitung Kalori','desc'=>'Biar kita tahu butuh berapa energi'],
                    ['step'=>'3','title'=>'Filter Alergi','desc'=>'Biar aman, pilah-pilah yang cocok'],
                    ['step'=>'4','title'=>'Menu Baru!','desc'=>'Voilà! Menu sehat siap kamu coba'],
                ] as $step)
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full bg-ng-orange text-white font-extrabold text-lg flex items-center justify-center mx-auto mb-4">{{ $step['step'] }}</div>
                        <h3 class="font-bold text-gray-800 mb-1">{{ $step['title'] }}</h3>
                        <p class="text-gray-500 text-sm">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-ng-orange py-16 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Siap Hidup Lebih Berwarna?</h2>
        <p class="text-orange-100 mb-8">Ribuan Gen Z sudah mulai perjalanannya. Giliran kamu!</p>
        <a href="{{ route('register') }}" class="bg-white text-ng-orange font-bold px-10 py-4 rounded-full hover:bg-orange-50 transition text-lg inline-block">
            Daftar Gratis Sekarang →
        </a>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-ng-dark-green text-green-300 text-center py-6 text-sm">
        <p>© {{ date('Y') }} NutriGo · Dibuat dengan 💚 untuk Indonesia yang lebih sehat</p>
    </footer>

</body>
</html>