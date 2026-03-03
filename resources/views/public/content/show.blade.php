@extends('layouts.public')

@section('title', $item->seo_title ?? $item->title)
@section('description', $item->seo_description ?? $item->excerpt_or_fallback)
@section('keywords', $item->seo_keywords)

@push('meta')
    <meta property="og:title" content="{{ $item->title }}">
    <meta property="og:description" content="{{ $item->excerpt_or_fallback }}">
    @if ($item->getFirstMediaUrl('featured_image'))
        <meta property="og:image" content="{{ $item->getFirstMediaUrl('featured_image') }}">
    @endif
    <meta property="og:url" content="{{ route('articles.show', [$item->type, $item->slug]) }}">
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="{{ $item->published_at->toIso8601String() }}">
    <meta property="article:author" content="{{ $item->author?->name }}">
@endpush

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Hero image --}}
        <div class="relative h-[40vh] md:h-[50vh] bg-[#1a1a1a]">
            <img src="{{ $item->getFirstMediaUrl('featured_image') ?? 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=1200&q=80' }}"
                alt="{{ $item->title }}" class="w-full h-full object-cover opacity-60">
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1a1a] via-transparent to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-start justify-between mb-4">
                        <a href="{{ route('articles.type', 'article') }}"
                            class="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Voltar
                        </a>

                        @if ($item->category)
                            <span class="bg-[#77c159] text-white text-xs font-semibold px-3 py-1 rounded-full">
                                {{ $item->category }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-tight">
                        {{ $item->title }}
                    </h1>
                </div>
            </div>
        </div>

        {{-- Article Meta --}}
        <div class="max-w-4xl mx-auto px-4 sm:px-6 -mt-4 relative z-10">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex flex-wrap items-center gap-6">
                @if ($item->author)
                    <div class="flex items-center gap-3">
                        @if (false)
                            {{-- @if ($item->author->getFirstMediaUrl('avatar') && false) --}}
                            <img src="{{ $item->author->getFirstMediaUrl('avatar') }}" alt="{{ $item->author->name }}"
                                class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-[#77c159]/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#77c159]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-semibold text-[#1a1a1a]">{{ $item->author->name }}</p>
                            <p class="text-xs text-gray-500">Autor</p>
                        </div>
                    </div>
                @endif

                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $item->published_at?->format('d M Y') ?? 'Recente' }}
                </div>

                @if ($item->tags->isNotEmpty())
                    <div class="flex items-center gap-2 flex-wrap">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        @foreach ($item->tags as $tag)
                            <span
                                class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Article Content --}}
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 md:py-16">
            @if ($item->excerpt)
                <p class="text-xl text-gray-600 leading-relaxed mb-8 border-l-4 border-[#77c159] pl-6 italic">
                    {{ $item->excerpt }}
                </p>
            @endif

            <div
                class="prose prose-lg max-w-none prose-headings:text-[#1a1a1a] prose-p:text-gray-700 prose-p:leading-relaxed prose-a:text-[#77c159] prose-a:no-underline hover:prose-a:underline prose-img:rounded-xl">
                {!! $item->content !!}
            </div>

            {{-- Comments --}}
            @php
                // Se ID existir, usa ele; senão usa slug
                $articleIdentifier = $item->id ?? $item->slug;
            @endphp
            <x-comment-section :article-id="$item->id" />

            {{-- Share --}}
            <div class="mt-12 pt-8 border-t border-gray-100 flex items-center gap-4">
                <span class="text-sm font-medium text-gray-500">Partilhar:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"
                    rel="noopener noreferrer"
                    class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 hover:bg-blue-100 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Related Articles --}}
        @if ($relatedItems->isNotEmpty())
            <section class="bg-gray-50 py-12 md:py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6">
                    <h2 class="text-2xl font-bold text-[#1a1a1a] mb-8">Artigos Relacionados</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($relatedItems as $related)
                            <a href="{{ route('articles.show', [$related->type, $related->slug]) }}" class="group block">
                                <div
                                    class="bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-lg transition-all">
                                    <img src="{{ $related->getFirstMediaUrl('featured_image') ?? 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=400&q=80' }}"
                                        alt="{{ $related->title }}"
                                        class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="p-4">
                                        <h3
                                            class="font-bold text-[#1a1a1a] group-hover:text-[#77c159] transition-colors line-clamp-2">
                                            {{ $related->title }}
                                        </h3>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
