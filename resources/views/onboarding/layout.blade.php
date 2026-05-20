<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setup Profil - NutriGo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-ng-cream flex items-center justify-center p-4">

<div class="w-full max-w-2xl">
    {{-- Progress Bar --}}
    <div class="mb-8">
        <div class="flex justify-between items-center mb-3">
            @foreach([1,2,3,4,5] as $s)
                <div class="flex items-center {{ $s < 5 ? 'flex-1' : '' }}">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold
                        {{ $currentStep >= $s ? 'bg-orange-500 text-white' : 'bg-white text-gray-400 border-2 border-gray-200' }}">
                        {{ $currentStep > $s ? '✓' : $s }}
                    </div>
                    @if($s < 5)
                        <div class="flex-1 h-1 mx-2 rounded {{ $currentStep > $s ? 'bg-orange-500' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
        <p class="text-center text-sm text-gray-500">Langkah {{ $currentStep }} dari 5</p>
    </div>

    @yield('content')
</div>
</body>
</html>