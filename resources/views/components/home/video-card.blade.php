{{-- resources/views/components/home/video-card.blade.php --}}
@props(['video', 'index' => 0])

@php
    $imageUrl = $video->getFirstMediaUrl('featured_image') 
        ?? ($video->cover_image ?? 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=600&q=80');
@endphp

<div
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, {{ $index * 50 }})"
    :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
    @click="$dispatch('video-selected', {
        id: {{ $video->id }},
        title: '{{ addslashes($video->title) }}',
        youtube_url: '{{ $video->youtube_url ?? '' }}',
        description: '{{ addslashes($video->excerpt ?? '') }}'
    })"
    class="group cursor-pointer h-full w-full transition-all duration-300">

    {{-- Video Card --}}
    <div class="relative w-full rounded-2xl overflow-hidden bg-[#1a1a1a]">
        <img src="{{ $imageUrl }}" 
             alt="{{ $video->title }}"
             class="w-full aspect-video object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">

        {{-- Play button --}}
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-[#77c159] flex items-center justify-center shadow-lg shadow-[#77c159]/30 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"></path>
                </svg>
            </div>
        </div>

        {{-- Info overlay --}}
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
            @if($video->category)
                <span class="inline-block bg-white/20 text-white text-[10px] uppercase tracking-wider px-2 py-1 rounded-full backdrop-blur-sm mb-2">
                    {{ $video->category }}
                </span>
            @endif
            <h3 class="text-white font-bold text-sm leading-tight line-clamp-2">
                {{ $video->title }}
            </h3>
            @if($video->speaker)
                <p class="text-white/70 text-xs mt-1">{{ $video->speaker }}</p>
            @endif
        </div>

        {{-- Duration badge --}}
        @if($video->duration)
            <div class="absolute top-3 right-3 bg-black/60 text-white text-xs px-2 py-1 rounded-md backdrop-blur-sm flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ $video->duration }}
            </div>
        @endif
    </div>
</div>