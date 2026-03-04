{{-- resources/views/public/index.blade.php --}}
@extends('layouts.public')

@section('title', 'Espaço Islâmico - Jornal Digital Islâmico em Português')

@section('description', 'O primeiro jornal digital islâmico em língua portuguesa dedicado a Moçambique e aos países
    lusófonos. Artigos, vídeos e investigação sobre o Islão.')

@section('content')
    @php
        use App\Domains\Content\Models\Article;
        use App\Domains\Content\Models\Category;

        // Buscar artigo em destaque (primeiro com featured flag ou o mais recente)
        $featuredArticle = Article::visible()
            ->with(['author', 'categories', 'tags'])
            ->where('type', 'article')
            ->latest('published_at')
            ->first();

        // Últimos artigos (excluindo o destaque)
        $latestArticles = Article::visible()
            ->with(['author', 'categories', 'tags'])
            ->where('type', 'article')
            // ->when($featuredArticle, function ($query) use ($featuredArticle) {
            //     return $query->where('id', '!=', $featuredArticle->id);
            // })
            ->latest('published_at')
            ->limit(6)
            ->get();

        // Vídeos recentes
        $videos = Article::visible()
            ->with(['author', 'categories', 'tags'])
            ->where('type', 'video')
            ->latest('published_at')
            ->limit(4)
            ->get();

        // Últimas edições de jornais
        $newspapers = Article::visible()->where('type', 'newspaper')->latest('published_at')->limit(3)->get();

        $categories = Category::active()->forContentType('article')->ordered()->get()->keyBy('slug')->toArray();

    @endphp

    <div class="min-h-screen bg-white">
        {{-- Hero Banner --}}
        <x-home.hero-banner :featured-article="$featuredArticle" />

        {{-- Category Bar --}}
        <x-home.category-bar :categories="$categories" />

        {{-- News Ticker --}}
        <x-home.news-ticker />

        {{-- Latest Articles --}}
        @if ($latestArticles->isNotEmpty())
        <section class="py-12 md:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-[#1a1a1a]">Últimos Artigos</h2>
                        <div class="w-16 h-1 bg-[#77c159] rounded-full mt-3"></div>
                    </div>
                    <a href="{{ route('articles.type', ['type' => 'article']) }}"
                        class="text-xs inline-flex items-center text-[#77c159] hover:text-[#5fa343] font-semibold transition-colors">
                        Ver todos
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                @if ($latestArticles->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($latestArticles as $index => $article)
                            <div x-data="{ show: false }" x-init="setTimeout(() => show = true, {{ $index * 100 }})" x-show="show"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <x-home.article-card :article="$article" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-gray-50 rounded-2xl">
                        <p class="text-gray-500">Artigos em breve...</p>
                    </div>
                @endif
            </div>
        </section>
        @endif

        {{-- Videos Section --}}
        @if ($videos->isNotEmpty())
        <section class="py-12 md:py-20 bg-[#f9fafb]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-[#1a1a1a]">Vídeos</h2>
                        <div class="w-16 h-1 bg-[#77c159] rounded-full mt-3"></div>
                    </div>
                    <a href="{{ route('articles.type', ['type' => 'video']) }}"
                        class="text-xs inline-flex items-center text-[#77c159] hover:text-[#5fa343] font-semibold transition-colors">
                        Ver todos
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                @if ($videos->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($videos as $index => $video)
                            <div x-data="{ show: false }" x-init="setTimeout(() => show = true, {{ $index * 100 }})" x-show="show"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <x-home.video-card :video="$video" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-white rounded-2xl">
                        <p class="text-gray-500">Vídeos em breve...</p>
                    </div>
                @endif
            </div>
        </section>
        @endif

        {{-- Newspapers Section --}}
        <section class="py-12 md:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-[#1a1a1a]">Últimas Edições</h2>
                        <div class="w-16 h-1 bg-[#77c159] rounded-full mt-3"></div>
                    </div>
                    <a href="{{ route('articles.type', ['type' => 'newspaper']) }}"
                        class="text-xs inline-flex items-center text-[#77c159] hover:text-[#5fa343] font-semibold transition-colors">
                        Ver todas
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <div
                    class="bg-gradient-to-br from-[#1a1a1a] to-[#2d2d2d] rounded-3xl p-8 md:p-12 flex flex-col md:flex-row items-center gap-8">
                    <div class="bg-white/5 rounded-2xl p-6 flex items-center justify-center">
                        <svg class="w-24 h-24 text-[#77c159]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <span
                            class="inline-block bg-[#77c159] text-white text-xs font-semibold px-3 py-1 rounded-full mb-3">
                            Semanário Digital
                        </span>
                        <h3 class="text-2xl font-bold text-white mb-3">Leia o Jornal Espaço Islâmico</h3>
                        <p class="text-gray-400 mb-6">Todas as edições disponíveis para leitura directa no site, sem
                            necessidade de download. Periodicidade semanal.</p>
                        <a href="{{ route('articles.type', ['type' => 'newspaper']) }}"
                            class="inline-flex items-center bg-[#77c159] hover:bg-[#5fa343] text-white rounded-full px-8 py-3 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                </path>
                            </svg>
                            Ler Jornais
                        </a>
                    </div>
                </div>

                {{-- @if ($newspapers->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    @foreach ($newspapers as $newspaper)
                        <div class="bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg transition-all">
                            <span class="text-sm text-[#77c159] font-medium">{{ $newspaper->published_at->format('d M Y') }}</span>
                            <h4 class="font-bold text-[#1a1a1a] mt-2">{{ $newspaper->title }}</h4>
                            <p class="text-gray-600 text-sm mt-2">{{ $newspaper->excerpt_or_fallback }}</p>
                            <a href="{{ route('articles.show', [$newspaper->type, $newspaper->slug]) }}" class="inline-flex items-center text-[#77c159] text-sm font-medium mt-4 hover:text-[#5fa343]">
                                Ler edição 
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif --}}
            </div>
        </section>

        {{-- Newsletter Section --}}
        <x-home.newsletter-section />


        {{-- Video Modal (only for videos) --}}
        <div x-data="{
            selectedVideo: null,
            getYouTubeId(url) {
                if (!url) return null;
                const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/);
                return match ? match[1] : null;
            }
        }" @video-selected.window="selectedVideo = $event.detail" x-cloak>

            <template x-if="selectedVideo">
                <div class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
                    @click="selectedVideo = null">
                    <div class="w-full max-w-4xl" @click.stop>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-white font-bold text-lg truncate pr-4" x-text="selectedVideo.title"></h3>
                            <button @click="selectedVideo = null" class="text-white/70 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <template x-if="getYouTubeId(selectedVideo.youtube_url)">
                            <div class="relative pb-[56.25%] rounded-xl overflow-hidden">
                                <iframe
                                    :src="`https://www.youtube.com/embed/${getYouTubeId(selectedVideo.youtube_url)}?autoplay=1`"
                                    class="absolute inset-0 w-full h-full"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </template>

                        <template x-if="!getYouTubeId(selectedVideo.youtube_url)">
                            <div class="bg-gray-800 rounded-xl aspect-video flex items-center justify-center">
                                <p class="text-gray-400">URL do vídeo não disponível</p>
                            </div>
                        </template>

                        <p x-show="selectedVideo.description" x-text="selectedVideo.description"
                            class="text-gray-400 text-sm mt-4">
                        </p>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Alpine.js components are defined inline in their respective components
        // This is just for any additional home page scripts
    </script>
@endpush
