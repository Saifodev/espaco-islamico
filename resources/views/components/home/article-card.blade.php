{{-- resources/views/components/home/article-card.blade.php --}}
@props(['article', 'index' => 0])

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

    $imageUrl = $article->getFirstMediaUrl('featured_image') ?? asset('placeholder.png');
@endphp

<div
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, {{ $index * 50 }})"
    :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
    onclick="window.location.href='{{ route('articles.show', [$article->type, $article->slug]) }}'"
    class="group cursor-pointer h-full w-full transition-all duration-300">

    {{-- Article Card --}}
    <article class="h-full bg-white rounded-2xl overflow-hidden border border-gray-100 hover:border-[#77c159]/30 hover:shadow-xl hover:shadow-[#77c159]/5 transition-all duration-300">
        <div class="relative overflow-hidden">
            <img src="{{ $imageUrl }}" 
                 alt="{{ $article->title }}"
                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">

            @if($article->category)
                <span class="absolute top-3 left-3 px-2 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider border {{ $categoryColors[$article->category] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                    {{ $article->category }}
                </span>
            @endif
        </div>

        <div class="p-5">
            <h3 class="font-bold text-lg text-[#1a1a1a] leading-tight group-hover:text-[#77c159] transition-colors mb-2 line-clamp-2">
                {{ $article->title }}
            </h3>

            <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-4">
                {{ $article->excerpt_or_fallback }}
            </p>

            @if($article->published_at && $article->author)
                <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray-400">{{ $article->published_at?->format('d M Y') }}</span>
                    </div>
                    <span class="text-xs font-medium text-gray-500">{{ $article->author->name }}</span>
                </div>
            @endif
        </div>
    </article>
</div>