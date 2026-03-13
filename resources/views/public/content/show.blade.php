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
    <article class="min-h-screen bg-white">
        {{-- Hero Image --}}
        <div class="relative h-[70vh] md:h-[80vh] bg-neutral-900">
            <img src="{{ $item->getFirstMediaUrl('featured_image') ?? 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=1920&q=80' }}"
                alt="" class="w-full h-full object-cover opacity-90" aria-hidden="true">

            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

            {{-- Navigation --}}
            <div class="absolute top-6 left-6 md:top-8 md:left-8 z-10">
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center gap-2 text-white/90 hover:text-white transition-colors outline border border-white/50 hover:border-white rounded-lg px-3 py-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="text-sm font-medium">Voltar</span>
                </a>
            </div>

            {{-- Category on Image --}}
            @if ($item->category)
                <div class="absolute bottom-6 left-6 md:bottom-8 md:left-8 z-10">
                    <span
                        class="inline-block px-4 py-1.5 bg-emerald-500 text-white text-sm font-semibold rounded-full shadow-lg">
                        {{ $item->category }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Article Content Area --}}
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12 md:py-16">
            {{-- Meta Information --}}
            <div class="flex flex-wrap items-center gap-6 text-sm mb-8 pb-6 border-b border-neutral-200">
                @if ($item->author)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                            <span class="text-emerald-600 font-semibold text-lg">
                                {{ substr($item->author->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <span class="block font-medium text-neutral-900">{{ $item->author->name }}</span>
                            <span class="text-neutral-500 text-xs">Autor</span>
                        </div>
                    </div>
                @endif

                <time class="flex items-center gap-2 text-neutral-500"
                    datetime="{{ $item->published_at?->toIso8601String() }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $item->published_at?->format('d F Y') ?? 'Publicação recente' }}
                </time>

                @if ($item->tags->isNotEmpty())
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        @foreach ($item->tags as $tag)
                            <span class="px-2 py-1 bg-neutral-100 text-neutral-600 text-xs rounded-full">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Title --}}
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-neutral-900 leading-tight mb-8">
                {{ $item->title }}
            </h1>

            {{-- Excerpt --}}
            @if ($item->excerpt)
                <div class="mb-10">
                    <p class="text-xl text-neutral-600 leading-relaxed border-l-4 border-emerald-500 pl-6 italic">
                        {{ $item->excerpt }}
                    </p>
                </div>
            @endif

            {{-- Main Content --}}
            <div
                class="prose prose-lg prose-neutral max-w-none
                        prose-headings:font-bold prose-headings:text-neutral-900
                        prose-p:text-neutral-700 prose-p:leading-relaxed
                        prose-a:text-emerald-600 prose-a:no-underline hover:prose-a:underline
                        prose-img:rounded-xl prose-img:shadow-lg">
                {!! $item->content !!}
            </div>

            {{-- Share Buttons --}}
            <div class="mt-12 pt-8 border-t border-neutral-200">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-neutral-500">Partilhar:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                        target="_blank" rel="noopener noreferrer"
                        class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Comments Section --}}
            <x-comment-section :article-id="$item->id" />
        </div>

        {{-- Related Articles --}}
        @if ($relatedItems->isNotEmpty())
            <aside class="bg-neutral-50 py-16 md:py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6">
                    <h2 class="text-2xl md:text-3xl font-bold text-neutral-900 mb-8">
                        Artigos Relacionados
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                        @foreach ($relatedItems as $related)
                            <a href="{{ route('articles.show', [$related->type, $related->slug]) }}" class="group block">
                                <article
                                    class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <div class="aspect-[16/9] overflow-hidden">
                                        <img src="{{ $related->getFirstMediaUrl('featured_image') ?? 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=600&q=80' }}"
                                            alt=""
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            aria-hidden="true">
                                    </div>
                                    <div class="p-5">
                                        <h3
                                            class="font-bold text-neutral-900 group-hover:text-emerald-600 transition-colors line-clamp-2">
                                            {{ $related->title }}
                                        </h3>
                                        @if ($related->excerpt)
                                            <p class="mt-2 text-sm text-neutral-600 line-clamp-2">
                                                {{ $related->excerpt }}
                                            </p>
                                        @endif
                                    </div>
                                </article>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        @endif
    </article>
@endsection
