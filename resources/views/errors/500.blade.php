<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>500 - Server Error | NutriGo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-ng-cream flex items-center justify-center p-6">
<div class="text-center max-w-md">
    <div class="text-8xl mb-6">⚙️</div>
    <h1 class="text-6xl font-extrabold text-gray-700 mb-2">500</h1>
    <h2 class="text-2xl font-bold text-gray-800 mb-3">Ada yang Salah di Dapur!</h2>
    <p class="text-gray-500 mb-8">Server kami sedang bermasalah. Tim kami sudah diberitahu. Coba lagi sebentar ya!</p>
    <a href="{{ route('home') }}" class="btn-primary">🔄 Coba Lagi</a>
</div>
</body>
</html>