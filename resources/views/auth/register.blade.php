<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - NutriGo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-ng-yellow via-ng-orange to-ng-red flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-extrabold text-white">nutri<span class="text-ng-dark-green">Go</span></h1>
    </div>
    <div class="bg-white rounded-3xl shadow-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Buat Akun</h2>
        <p class="text-gray-500 text-sm mb-6">Mulai perjalanan sehatmu hari ini!</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input-field" placeholder="Nama kamu" required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input-field" placeholder="nama@email.com" required>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Password</label>
                    <input type="password" name="password" class="input-field" placeholder="Min. 8 karakter" required>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="input-field" placeholder="Ulangi password" required>
                </div>
                <button type="submit" class="btn-primary w-full text-center">Daftar Sekarang 🚀</button>
            </div>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-ng-orange font-semibold hover:underline">Masuk</a>
        </p>
    </div>
</div>
</body>
</html>