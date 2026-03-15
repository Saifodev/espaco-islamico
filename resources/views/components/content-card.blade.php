@props(['item', 'type', 'index' => 0])

@php

    // Cores por categoria (artigos)
    $categoryColors = [
        'Fé' => 'bg-blue-50 text-blue-700 border-blue-200',
        'Sharia' => 'bg-purple-50 text-purple-700 border-purple-200',
        'História' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Família' => 'bg-pink-50 text-pink-700 border-pink-200',
        'Ciência' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
        'Sociedade' => 'bg-orange-50 text-orange-700 border-orange-200',
        'Educação' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Ramadan' => 'bg-green-50 text-green-700 border-green-200',
        'Juventude' => 'bg-rose-50 text-rose-700 border-rose-200',
    ];

    $imageUrl =
        $item->getFirstMediaUrl('featured_image') ?? asset('placeholder.png');
@endphp

<div x-data="{ show: false }" x-init="setTimeout(() => show = true, {{ $index * 50 }})"
    :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
    @click="
        @if ($type === 'video')
            $dispatch('video-selected', {
                id: {{ $item->id }},
                title: @js($item->title),
                youtube_url: @js($item->youtube_url),
                description: @js($item->excerpt)
            })
        @elseif($type === 'newspaper')
        @if (!$item->is_sellable)
        $dispatch('open-magazine', {
                    id: {{ $item->id }},
                    title: @js($item->title),
                    edition: @js($item->edition),
                    pdf_url: @js($item->getFirstMediaUrl('documents'))
                })
        @endif
        @else
        window.location.href='{{ route('articles.show', [$item->type, $item->slug]) }}'
        @endif
    "
    class="group cursor-pointer h-full w-full transition-all duration-300">

    @if ($type === 'newspaper')
        {{-- Newspaper Card --}}
        <div
            class="relative bg-gray-100 rounded-xl overflow-hidden aspect-[3/4] mb-3 shadow-md group-hover:shadow-xl transition-shadow duration-300">
            @if (false)
                <img src="{{ $imageUrl }}" alt="{{ $item->title }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div
                    class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#1a1a1a] to-[#2d2d2d] p-4">
                    <svg class="w-12 h-12 text-[#77c159] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                        </path>
                    </svg>
                    <p class="text-white text-xs text-center font-medium leading-tight">{{ $item->title }}</p>
                </div>
            @endif

            @if ($item->edition)
                <span
                    class="absolute top-3 left-3 px-2 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider border bg-[#77c159] text-white border-[#77c159]">
                    {{ $item->edition }}
                </span>
            @endif

            @if ($item->is_sellable)
                <div class="absolute inset-0 flex items-center justify-center transition-all duration-300">
                    <a href="{{ $item->whatsapp_link }}" target="_blank" @click.stop
                        class="opacity-0 group-hover:opacity-100 transition-opacity bg-[#77c159] text-white text-xs font-semibold px-4 py-2 rounded-full group-hover:bg-opacity-100 bg-opacity-0">
                        Comprar por {{ number_format($item->price, 2, ',', '.') }} MT
                    </a>
                </div>
            @else
                <div class="absolute inset-0 flex items-center justify-center transition-all duration-300">
                    <div
                        class="opacity-0 group-hover:opacity-100 transition-opacity bg-[#77c159] text-white text-xs font-semibold px-4 py-2 rounded-full group-hover:bg-opacity-100 bg-opacity-0">
                        Ler Agora
                    </div>
                </div>
            @endif

            {{-- @if ($item->edition)
                <span
                    class="absolute top-2 left-2 bg-[#77c159] text-white text-[10px] font-semibold px-2 py-1 rounded-full">
                    Edição {{ $item->edition }}
                </span>
            @endif --}}
        </div>
        <h3
            class="text-sm font-semibold text-[#1a1a1a] line-clamp-2 leading-tight group-hover:text-[#77c159] transition-colors">
            {{ $item->title }}
        </h3>
        @if ($item->published_at)
            <p class="text-xs text-gray-400 mt-1">{{ $item->published_at->format('d M Y') }}</p>
        @endif
    @elseif($type === 'video')
        {{-- Video Card --}}
        <div class="relative w-full rounded-2xl overflow-hidden bg-[#1a1a1a]">
            <img src="{{ $imageUrl }}" alt="{{ $item->title }}"
                class="w-full aspect-video object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">

            {{-- <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" /> --}}

            {{-- Play button --}}
            <div class="absolute inset-0 flex items-center justify-center">
                <div
                    class="w-16 h-16 rounded-full bg-[#77c159] flex items-center justify-center shadow-lg shadow-[#77c159]/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"></path>
                    </svg>
                </div>
            </div>

            {{-- Info --}}
            <div class="absolute bottom-0 left-0 right-0 p-4">
                @if ($item->category)
                    <span
                        class="inline-block bg-white/20 text-white text-[10px] uppercase tracking-wider px-2 py-1 rounded-full backdrop-blur-sm mb-2">
                        {{ $item->category }}
                    </span>
                @endif
                <h3 class="text-white font-bold text-sm leading-tight line-clamp-2">
                    {{ $item->title }}
                </h3>
                @if ($item->speaker)
                    <p class="text-white/70 text-xs mt-1">{{ $item->speaker }}</p>
                @endif
            </div>

            @if ($item->duration)
                <div
                    class="absolute top-3 right-3 bg-black/60 text-white text-xs px-2 py-1 rounded-md backdrop-blur-sm flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $item->duration }}
                </div>
            @endif
        </div>
    @else
        {{-- Article Card --}}
        <article
            class="h-full bg-white rounded-2xl overflow-hidden border border-gray-100 hover:border-[#77c159]/30 hover:shadow-xl hover:shadow-[#77c159]/5 transition-all duration-300">
            <div class="relative overflow-hidden">
                <img src="{{ $imageUrl }}" alt="{{ $item->title }}"
                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">

                @if ($item->category)
                    <span
                        class="absolute top-3 left-3 px-2 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider border {{ $categoryColors[$item->category] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                        {{ $item->category }}
                    </span>
                @endif
            </div>

            <div class="p-5">
                {{-- <div class="flex items-center text-xs text-gray-400 mb-2">
                    
                    <span class="mx-2">•</span>
                    <span>{{ $item->reading_time_in_minutes }}</span>
                </div> --}}

                <h3
                    class="font-bold text-lg text-[#1a1a1a] leading-tight group-hover:text-[#77c159] transition-colors mb-2 line-clamp-2">
                    {{ $item->title }}
                </h3>

                <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-4">
                    {!! $item->excerpt_or_fallback !!}
                </p>

                @if ($item->published_at && $item->author)
                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span
                                class="text-xs font-medium text-gray-400">{{ $item->published_at?->format('d M Y') }}</span>
                        </div>
                        <span class="text-xs font-medium text-gray-500">{{ $item->author->name }}</span>
                    </div>
                @endif
            </div>
        </article>
    @endif
</div>
