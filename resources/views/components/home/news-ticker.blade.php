{{-- resources/views/components/home/news-grid.blade.php --}}
@props(['news' => []])

@php
    $items = $news ?? [];
@endphp

<div class="bg-[#1a1a1a] py-10 md:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-2">
                <div class="w-1 h-6 bg-[#77c159] rounded-full"></div>
                <h2 class="text-white font-bold text-lg uppercase tracking-wide">
                    Últimas Notícias
                </h2>
            </div>

            <a href="https://www.facebook.com/profile.php?id=61570540160741" target="_blank"
                class="text-xs text-gray-400 hover:text-[#77c159] transition-colors">
                Ver no Facebook
            </a>
        </div>

        {{-- GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-4 grid-rows-3 gap-6 h-[420px] overflow-hidden">

            @foreach ($items as $i => $article)
                @php
                    $layout = match ($i) {
                        0 => 'md:col-span-2 md:row-span-2', // destaque grande
                        1 => 'md:col-span-2', // destaque horizontal
                        2 => 'md:row-span-2', // vertical
                        default => '',
                    };
                @endphp

                <a href="{{ route('articles.show', ['news', $article['slug']]) }}"
                    class="group relative rounded-2xl overflow-hidden {{ $layout }}">

                    {{-- Image --}}
                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                        class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">

                    {{-- Gradient --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                    {{-- Text --}}
                    <div class="absolute bottom-0 p-5">

                        <p class="text-[#77c159] text-xs font-semibold uppercase mb-2">
                            {{ $article['date'] ?? '' }}
                        </p>

                        <h3 class="text-white font-bold text-lg leading-snug group-hover:text-[#77c159] transition">
                            {{ $article['title'] }}
                        </h3>

                    </div>

                </a>
            @endforeach

        </div>

    </div>
</div>
