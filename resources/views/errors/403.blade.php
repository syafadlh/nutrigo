<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>403 - Akses Ditolak | NutriGo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-ng-cream flex items-center justify-center p-6">
<div class="text-center max-w-md">
    <div class="text-8xl mb-6">🚫</div>
    <h1 class="text-6xl font-extrabold text-red-500 mb-2">403</h1>
    <h2 class="text-2xl font-bold text-gray-800 mb-3">Akses Ditolak!</h2>
    <p class="text-gray-500 mb-8">Kamu tidak punya izin untuk mengakses halaman ini.</p>
    <a href="{{ route('home') }}" class="btn-primary">🏠 Kembali ke Beranda</a>
</div>
</body>
</html>