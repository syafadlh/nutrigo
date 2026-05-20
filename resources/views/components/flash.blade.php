@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-transition
    x-init="setTimeout(() => show = false, 4000)"
    class="fixed bottom-6 right-6 z-50 bg-ng-dark-green text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 max-w-sm">
    <span class="text-xl">✅</span>
    <p class="text-sm font-medium">{{ session('success') }}</p>
    <button @click="show = false" class="ml-2 text-green-300 hover:text-white text-lg leading-none">×</button>
</div>
@endif

@if(session('error') || $errors->any())
<div x-data="{ show: true }" x-show="show" x-transition
    x-init="setTimeout(() => show = false, 6000)"
    class="fixed bottom-6 right-6 z-50 bg-red-600 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 max-w-sm">
    <span class="text-xl">⚠️</span>
    <div>
        @if(session('error'))
            <p class="text-sm font-medium">{{ session('error') }}</p>
        @else
            @foreach($errors->all() as $e)
                <p class="text-sm">{{ $e }}</p>
            @endforeach
        @endif
    </div>
    <button @click="show = false" class="ml-2 text-red-200 hover:text-white text-lg leading-none">×</button>
</div>
@endif