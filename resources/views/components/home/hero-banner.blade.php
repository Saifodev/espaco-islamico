{{-- resources/views/components/home/hero-banner.blade.php --}}
@props([
    'featuredArticle' => null,
    'useImageBackground' => true,
])

@php
    $defaultImage = asset('placeholder.png');
@endphp

<section
    class="relative overflow-hidden min-h-[70vh] md:min-h-[80vh] flex items-center isolate
    {{ $useImageBackground
        ? 'bg-cover bg-center bg-no-repeat'
        : 'bg-gradient-to-br from-[#1a1a1a] via-[#2d2d2d] to-[#1a1a1a]' }}"
    @if ($useImageBackground) style="background-image: url('{{ asset('bg3.jpg') }}');" @endif>

    {{-- Dark Overlay for readability --}}
    @if ($useImageBackground)
        <div class="absolute inset-0 bg-black/70 backdrop-blur-[2px]"></div>
    @endif

    {{-- Geometric Pattern Overlay --}}
    @if (!$useImageBackground)
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
        style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
    </div>
    @endif

    {{-- Green Accent Glow --}}
    <div
        class="absolute top-0 right-0 w-[600px] h-[600px] bg-[#77c159] rounded-full blur-[200px] opacity-10 pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-[#77c159] rounded-full blur-[150px] opacity-5 pointer-events-none">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-16 md:py-24 w-full">
        <div class="grid md:grid-cols-2 gap-12 items-center">

            {{-- LEFT CONTENT --}}
            <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)" x-show="show"
                x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-y-8"
                x-transition:enter-end="opacity-100 translate-y-0">

                <div
                    class="inline-flex items-center gap-2 bg-[#77c159]/10 border border-[#77c159]/20 rounded-full px-4 py-1.5 mb-6">
                    <span class="w-2 h-2 rounded-full bg-[#77c159] animate-pulse"></span>
                    <span class="text-[#77c159] text-xs font-medium uppercase tracking-wider">
                        Jornal Digital Islâmico
                    </span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-[1.1] mb-6">
                    Informar, Educar e
                    <span class="text-[#77c159]">Inspirar</span>
                    a Comunidade
                </h1>

                <p class="text-lg text-gray-300 mb-8 max-w-lg leading-relaxed">
                    O primeiro jornal digital islâmico em língua portuguesa dedicado a Moçambique e aos países
                    lusófonos.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('articles.type', ['type' => 'article']) }}"
                        class="inline-flex items-center bg-[#77c159] hover:bg-[#5fa343] text-white rounded-full px-8 py-3 text-base font-semibold shadow-lg shadow-[#77c159]/20 transition-all hover:shadow-xl hover:shadow-[#77c159]/30">
                        Ler Artigos
                    </a>

                    <a href="{{ route('articles.type', ['type' => 'video']) }}"
                        class="inline-flex items-center rounded-full px-8 py-3 text-base font-semibold bg-white text-[#77c159] hover:bg-[#77c159] hover:text-white transition-all">
                        Ver Vídeos
                    </a>
                </div>
            </div>

            {{-- RIGHT FEATURED --}}
            @if ($featuredArticle)
                <div class="hidden md:block">
                    <a href="{{ route('articles.show', [$featuredArticle->type, $featuredArticle->slug]) }}"
                        class="relative group block">

                        <div class="rounded-2xl overflow-hidden shadow-2xl">
                            <img src="{{ $featuredArticle->getFirstMediaUrl('featured_image') ?: $defaultImage }}"
                                alt="{{ $featuredArticle->title }}"
                                class="w-full aspect-[4/3] object-cover group-hover:scale-105 transition-transform duration-500">

                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent">
                            </div>

                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <span
                                    class="inline-block bg-[#77c159] text-white text-xs font-semibold px-3 py-1 rounded-full mb-3 uppercase tracking-wider">
                                    Em Destaque
                                </span>

                                <h3
                                    class="text-xl font-bold text-white leading-tight group-hover:text-[#77c159] transition-colors">
                                    {{ $featuredArticle->title }}
                                </h3>

                                <p class="text-sm text-gray-300 mt-2 line-clamp-2">
                                    {{ $featuredArticle->excerpt_or_fallback }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

        </div>
    </div>
</section>
