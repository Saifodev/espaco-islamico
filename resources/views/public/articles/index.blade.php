@extends('layouts.public')

@section('title', $title . ' - Espaço Islâmico')
@section('description', 'Confira os ' . strtolower($title) . ' do Espaço Islâmico')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $title }}</h1>
        @if($type)
            <p class="text-lg text-gray-600">
                @if($type === 'article')
                    Artigos, análises e reflexões sobre o mundo islâmico
                @elseif($type === 'video')
                    Palestras, entrevistas e conteúdo em vídeo
                @elseif($type === 'newspaper')
                    Edições completas do Jornal Espaço Islâmico
                @endif
            </p>
        @endif
    </div>

    {{-- Articles Grid --}}
    @if($articles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($articles as $article)
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    @if($article->getFirstMediaUrl('featured_image'))
                        <img src="{{ $article->getFirstMediaUrl('featured_image') }}" 
                             alt="{{ $article->title }}"
                             class="w-full h-48 object-cover">
                    @endif
                    
                    <div class="p-6">
                        {{-- Meta --}}
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <span>{{ $article->published_at->format('d/m/Y') }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $article->reading_time_in_minutes }}</span>
                            @if($article->type !== 'article')
                                <span class="mx-2">•</span>
                                <span class="capitalize">{{ $article->type }}</span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">
                            <a href="{{ route('articles.show', $article->slug) }}" 
                               class="hover:text-[var(--brand-green)] transition">
                                {{ $article->title }}
                            </a>
                        </h2>

                        {{-- Excerpt --}}
                        <p class="text-gray-600 mb-4">
                            {{ $article->excerpt_or_fallback }}
                        </p>

                        {{-- Categories --}}
                        @if($article->categories->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($article->categories as $category)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Read More --}}
                        <a href="{{ route('articles.show', $article->slug) }}" 
                           class="text-[var(--brand-green)] hover:text-[var(--brand-green-dark)] text-sm font-medium inline-flex items-center">
                            Ler mais
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $articles->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">Nenhum conteúdo encontrado.</p>
        </div>
    @endif
</div>
@endsection