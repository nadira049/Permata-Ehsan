@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-4">
        <ul class="flex rounded-xl overflow-hidden bg-gray-800 text-white shadow divide-x divide-gray-700">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="px-4 py-2 opacity-50 cursor-not-allowed select-none">&lt;</li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-4 py-2 hover:bg-gray-700 transition">&lt;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="px-4 py-2">{{ $element }}</li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="px-4 py-2 bg-gray-900 font-bold">{{ $page }}</li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="px-4 py-2 hover:bg-gray-700 transition">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-4 py-2 hover:bg-gray-700 transition">&gt;</a>
                </li>
            @else
                <li class="px-4 py-2 opacity-50 cursor-not-allowed select-none">&gt;</li>
            @endif
        </ul>
    </nav>
@endif 