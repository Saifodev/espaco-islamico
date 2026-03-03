{{-- resources/views/components/home/hero-banner.blade.php --}}
@props(['featuredArticle' => null])

@php
    $defaultImage = 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=800&q=80';
@endphp

<section class="relative overflow-hidden bg-gradient-to-br from-[#1a1a1a] via-[#2d2d2d] to-[#1a1a1a] min-h-[70vh] md:min-h-[80vh] flex items-center">
    {{-- Geometric Pattern Overlay --}}
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    {{-- Green accent glow --}}
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-[#77c159] rounded-full blur-[200px] opacity-10"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-[#77c159] rounded-full blur-[150px] opacity-5"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-16 md:py-24 w-full">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div x-data="{ show: false }" 
                 x-init="setTimeout(() => show = true, 100)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0">
                
                <div class="inline-flex items-center gap-2 bg-[#77c159]/10 border border-[#77c159]/20 rounded-full px-4 py-1.5 mb-6">
                    <span class="w-2 h-2 rounded-full bg-[#77c159] animate-pulse"></span>
                    <span class="text-[#77c159] text-xs font-medium uppercase tracking-wider">Jornal Digital Islâmico</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-[1.1] mb-6">
                    Informar, Educar e 
                    <span class="text-[#77c159]">Inspirar</span> 
                    a Comunidade
                </h1>
                
                <p class="text-lg text-gray-400 mb-8 max-w-lg leading-relaxed">
                    O primeiro jornal digital islâmico em língua portuguesa dedicado a Moçambique e aos países lusófonos. Artigos, vídeos e investigação.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('articles.type', ['type' => 'article']) }}" 
                       class="inline-flex items-center bg-[#77c159] hover:bg-[#5fa343] text-white rounded-full px-8 py-3 text-base font-semibold shadow-lg shadow-[#77c159]/20 transition-all hover:shadow-xl hover:shadow-[#77c159]/30">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Ler Artigos
                    </a>
                    <a href="{{ route('articles.type', ['type' => 'video']) }}" 
                       class="inline-flex items-center rounded-full px-8 py-3 text-base font-semibold bg-white text-[#77c159] hover:bg-[#77c159] hover:text-white transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ver Vídeos
                    </a>
                </div>
            </div>

            @if($featuredArticle)
                <div x-data="{ show: false }" 
                     x-init="setTimeout(() => show = true, 300)"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-x-8"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="hidden md:block">
                    
                    <a href="{{ route('articles.show', [$featuredArticle->type, $featuredArticle->slug]) }}" class="relative group block">
                        <div class="rounded-2xl overflow-hidden shadow-2xl">
                            <img src="{{ $featuredArticle->getFirstMediaUrl('featured_image') ?: $defaultImage }}"
                                 alt="{{ $featuredArticle->title }}"
                                 class="w-full aspect-[4/3] object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <span class="inline-block bg-[#77c159] text-white text-xs font-semibold px-3 py-1 rounded-full mb-3 uppercase tracking-wider">
                                    Em Destaque
                                </span>
                                <h3 class="text-xl font-bold text-white leading-tight group-hover:text-[#77c159] transition-colors">
                                    {{ $featuredArticle->title }}
                                </h3>
                                <p class="text-sm text-gray-300 mt-2 line-clamp-2">{{ $featuredArticle->excerpt_or_fallback }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>