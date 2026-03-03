@extends('layouts.public')

@section('title', $article->seo_title ?? $article->title)
@section('description', $article->seo_description ?? $article->excerpt_or_fallback)
@section('keywords', $article->seo_keywords)

@push('meta')
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description" content="{{ $article->excerpt_or_fallback }}">
    @if($article->getFirstMediaUrl('featured_image'))
        <meta property="og:image" content="{{ $article->getFirstMediaUrl('featured_image') }}">
    @endif
    <meta property="og:url" content="{{ route('articles.show', $article->slug) }}">
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="{{ $article->published_at->toIso8601String() }}">
    <meta property="article:author" content="{{ $article->author->name }}">
@endpush

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <header class="mb-8">
        {{-- Meta --}}
        <div class="flex items-center text-sm text-gray-500 mb-4">
            <span>{{ $article->published_at->format('d/m/Y H:i') }}</span>
            <span class="mx-2">•</span>
            <span>{{ $article->reading_time_in_minutes }} de leitura</span>
            @if($article->type !== 'article')
                <span class="mx-2">•</span>
                <span class="capitalize">{{ $article->type }}</span>
            @endif
        </div>

        {{-- Title --}}
        <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $article->title }}</h1>

        {{-- Excerpt --}}
        @if($article->excerpt)
            <p class="text-xl text-gray-600 leading-relaxed">{{ $article->excerpt }}</p>
        @endif

        {{-- Author --}}
        <div class="flex items-center mt-6">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-[var(--brand-green-light)] flex items-center justify-center">
                    <span class="text-[var(--brand-green)] font-medium">
                        {{ substr($article->author->name, 0, 1) }}
                    </span>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ $article->author->name }}</p>
                <p class="text-sm text-gray-500">Autor</p>
            </div>
        </div>
    </header>

    {{-- Featured Image --}}
    @if($article->getFirstMediaUrl('featured_image'))
        <div class="mb-8">
            <img src="{{ $article->getFirstMediaUrl('featured_image') }}" 
                 alt="{{ $article->title }}"
                 class="w-full rounded-lg shadow-lg">
        </div>
    @endif

    {{-- Content --}}
    <div class="prose prose-lg max-w-none mb-12">
        {!! $article->content !!}
    </div>

    {{-- Categories --}}
    @if($article->categories->isNotEmpty())
        <div class="mt-8 pt-8 border-t">
            <h2 class="text-sm font-medium text-gray-500 mb-3">Categorias:</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($article->categories as $category)
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                        {{ $category->name }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tags --}}
    @if($article->tags->isNotEmpty())
        <div class="mt-4">
            <h2 class="text-sm font-medium text-gray-500 mb-3">Tags:</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($article->tags as $tag)
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm">
                        #{{ $tag->name }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Related Articles --}}
    @if($relatedArticles->isNotEmpty())
        <div class="mt-12 pt-8 border-t">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Artigos relacionados</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedArticles as $related)
                    <a href="{{ route('articles.show', $related->slug) }}" class="group block">
                        @if($related->getFirstMediaUrl('featured_image'))
                            <img src="{{ $related->getFirstMediaUrl('featured_image') }}" 
                                 alt="{{ $related->title }}"
                                 class="w-full h-32 object-cover rounded-lg mb-2">
                        @endif
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-[var(--brand-green)] transition">
                            {{ $related->title }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $related->published_at->format('d/m/Y') }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Share Buttons --}}
    <div class="mt-12 pt-8 border-t">
        <h2 class="text-sm font-medium text-gray-500 mb-4">Compartilhe:</h2>
        <div class="flex gap-3">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('articles.show', $article->slug)) }}" 
               target="_blank"
               rel="noopener noreferrer"
               class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                </svg>
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('articles.show', $article->slug)) }}&text={{ urlencode($article->title) }}" 
               target="_blank"
               rel="noopener noreferrer"
               class="p-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                </svg>
            </a>
            <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' ' . route('articles.show', $article->slug)) }}" 
               target="_blank"
               rel="noopener noreferrer"
               class="p-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 1.943.552 3.773 1.51 5.335L2.07 21.93l4.595-1.44A9.97 9.97 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm3.95 14.228c-.313.88-1.55 1.61-2.54 1.77-.677.11-1.55.14-2.49-.13-.83-.23-1.68-.7-2.64-1.36-2.2-1.5-3.63-4.04-3.74-4.23-.11-.19-.89-1.46-.89-2.77 0-1.31.67-1.95.94-2.23.22-.23.58-.31.89-.31h.62c.2 0 .45.03.69.54.24.5.8 1.98.87 2.12.07.14.12.31.03.49-.09.18-.14.29-.27.45-.14.16-.27.28-.4.44-.13.15-.28.32-.12.61.16.29.72 1.19 1.55 1.93 1.07.95 1.97 1.25 2.26 1.39.29.14.46.12.63-.07.17-.19.72-.83.91-1.12.19-.29.38-.24.64-.14.26.1 1.64.77 1.92.91.28.14.47.21.54.33.07.12.07.69-.24 1.38z" clip-rule="evenodd"></path>
                </svg>
            </a>
        </div>
    </div>
</article>
@endsection