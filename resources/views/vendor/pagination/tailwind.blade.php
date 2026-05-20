@if ($paginator->hasPages())
<nav class="flex items-center justify-between mt-4" aria-label="Pagination">
    <div class="text-sm text-gray-500">
        Menampilkan <span class="font-semibold text-gray-700">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-gray-700">{{ $paginator->lastItem() }}</span>
        dari <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span> data
    </div>

    <div class="flex items-center gap-1">
        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-sm text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">←</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-ng-orange hover:text-ng-orange transition">←</a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2 text-sm text-gray-400">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-2 text-sm font-semibold text-white bg-ng-orange border border-ng-orange rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-ng-orange hover:text-ng-orange transition">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-ng-orange hover:text-ng-orange transition">→</a>
        @else
            <span class="px-3 py-2 text-sm text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">→</span>
        @endif
    </div>
</nav>
@endif