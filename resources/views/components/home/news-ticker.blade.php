{{-- resources/views/components/home/news-grid.blade.php --}}
@props(['news' => []])

@php
    $items = $news ?? [];
@endphp

<section class="bg-[#1a1a1a] py-10 md:py-14">

    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-8">

            <div class="flex items-center gap-3">
                <div class="w-1 h-6 bg-[#77c159] rounded-full"></div>

                <h2 class="text-white font-bold text-lg uppercase tracking-wide">
                    Últimas Notícias
                </h2>
            </div>

            <a href="https://www.facebook.com/profile.php?id=61570540160741" target="_blank"
                class="text-xs text-gray-400 hover:text-[#77c159] transition">
                Ver no Facebook
            </a>

        </div>

        {{-- GRID --}}
        <div
            class="grid gap-6
            grid-cols-1
            sm:grid-cols-2
            lg:grid-cols-4
            auto-rows-[160px]
            md:auto-rows-[170px]
            lg:auto-rows-[180px]">

            @foreach ($items as $i => $article)
                @php
                    $layout = match ($i) {
                        0 => 'lg:col-span-2 lg:row-span-2',
                        1 => 'lg:col-span-2',
                        2 => 'lg:row-span-2',
                        default => '',
                    };
                @endphp

                <a href="{{ route('articles.show', ['news', $article['slug']]) }}"
                    class="group relative overflow-hidden rounded-2xl {{ $layout }}">

                    {{-- IMAGE --}}
                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" loading="lazy"
                        class="w-full h-full object-cover transition duration-500 group-hover:scale-105">

                    {{-- OVERLAY --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>

                    {{-- TEXT --}}
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

</section>
