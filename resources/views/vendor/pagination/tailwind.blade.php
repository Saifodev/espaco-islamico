@if ($paginator->hasPages())
    <nav class="flex items-center justify-center">
        <div class="flex items-center gap-2 bg-white rounded-2xl shadow-sm p-2">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-gray-300 cursor-not-allowed">
                    ‹
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-2 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    ‹
                </a>
            @endif


            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-gray-400">...</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="px-4 py-2 rounded-xl bg-[#77c159] text-white font-semibold shadow">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach


            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-2 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    ›
                </a>
            @else
                <span class="px-3 py-2 text-gray-300 cursor-not-allowed">
                    ›
                </span>
            @endif

        </div>
    </nav>
@endif