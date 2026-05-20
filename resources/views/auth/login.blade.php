    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Login - NutriGo</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gradient-to-br from-ng-yellow via-ng-orange to-ng-red flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-white">nutri<span class="text-ng-dark-green">Go</span></h1>
            <p class="text-white/80 mt-2">Temukan menu sehat untukmu setiap hari 🥗</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-1">Welcome Back!</h2>
            <p class="text-gray-500 text-sm mb-6">Lanjutkan perjalanan sehatmu bersama NutriGo</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="input-field" placeholder="nama@email.com" required>
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1">Password</label>
                        <input type="password" name="password" class="input-field" placeholder="••••••••" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded">
                            Remember me
                        </label>
                    </div>
                    <button type="submit" class="btn-primary w-full text-center">Masuk Sekarang</button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Belum punya akun? <a href="{{ route('register') }}" class="text-ng-orange font-semibold hover:underline">Daftar NutriGo</a>
            </p>
        </div>
    </div>
    </body>
    </html>