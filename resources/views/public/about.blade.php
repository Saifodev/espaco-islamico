@php
    $useImageBackground = true;
@endphp

@extends('layouts.public')

@section('title', 'Sobre Nós - Espaço Islâmico')
@section('description',
    'Conheça a história, missão e valores do Espaço Islâmico - Jornal Digital dedicado à comunidade
    muçulmana de Moçambique e PALOP.')

@section('content')
    <div class="min-h-screen bg-white">

        {{-- Hero --}}
        <div class="relative py-20 md:py-28 overflow-hidden isolate 
            {{ $useImageBackground ? 'bg-cover bg-center bg-no-repeat' : 'bg-gradient-to-br from-[#1a1a1a] to-[#2d2d2d]' }}"
            @if ($useImageBackground) style="background-image: url('{{ asset('bg1.png') }}');" @endif>

            {{-- Dark overlay for readability --}}
            @if ($useImageBackground)
                <div class="absolute inset-0 bg-black/75 backdrop-blur-[3px]"></div>
            @endif

            {{-- Soft green glow --}}
            <div
                class="absolute top-0 right-0 w-[500px] h-[500px] bg-[#77c159] rounded-full blur-[200px] opacity-10 pointer-events-none">
            </div>

            {{-- Optional subtle grain overlay (professional touch) --}}
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
                style="background-image: url('data:image/svg+xml,%3Csvg viewBox=\'0 0 200 200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'noiseFilter\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.65\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23noiseFilter)\'/%3E%3C/svg%3E');">
            </div>

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 text-center">
                <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)" x-show="show"
                    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-6"
                    x-transition:enter-end="opacity-100 translate-y-0">

                    <img src="https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/user_69a0952abe7cb8c0c94a27c4/13aba247d_logo.png"
                        alt="Espaço Islâmico" class="h-24 md:h-32 mx-auto mb-6 drop-shadow-lg">

                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        Espaço Islâmico
                    </h1>

                    <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed">
                        Um espaço dedicado ao mundo Islâmico — informação, educação e inspiração para a comunidade lusófona.
                    </p>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        @php
            $stats = [
                ['icon' => 'book-open', 'label' => 'Artigos Publicados', 'value' => '20+'],
                ['icon' => 'users', 'label' => 'Seguidores', 'value' => '24.500+'],
                ['icon' => 'globe', 'label' => 'Países Lusófonos', 'value' => 'PALOP'],
            ];
        @endphp

        <div class="max-w-5xl mx-auto px-4 sm:px-6 -mt-10 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($stats as $index => $stat)
                    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, {{ $index * 100 }})" x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 text-center">

                        <div class="w-12 h-12 rounded-xl bg-[#77c159]/10 flex items-center justify-center mx-auto mb-3">
                            @if ($stat['icon'] === 'book-open')
                                <svg class="w-6 h-6 text-[#77c159]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            @elseif($stat['icon'] === 'users')
                                <svg class="w-6 h-6 text-[#77c159]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            @elseif($stat['icon'] === 'globe')
                                <svg class="w-6 h-6 text-[#77c159]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            @endif
                        </div>
                        <p class="text-3xl font-bold text-[#1a1a1a]">{{ $stat['value'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Story --}}
        <section class="max-w-5xl mx-auto px-4 sm:px-6 py-16 md:py-20">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-[#1a1a1a] mb-6">A Nossa História</h2>
                    <div class="space-y-4 text-gray-600 leading-relaxed">
                        <p>
                            Criado no início de 2025 na província de Nampula, com sede actual na Cidade de Maputo,
                            o Jornal <strong>Espaço Islâmico</strong> nasceu com a missão de servir como ponte de informação
                            e educação para a comunidade muçulmana de Moçambique e dos países lusófonos.
                        </p>
                        <p>
                            Reconhecido como referência na promoção do Islão em Moçambique, o Espaço Islâmico
                            publica artigos, investigações e conteúdo multimédia que abrange temas como fé,
                            história islâmica, família, educação, juventude e sociedade.
                        </p>
                        <p>
                            Com mais de 24.500 seguidores nas redes sociais, continuamos a crescer e a expandir
                            o nosso alcance, levando conhecimento islâmico de qualidade em língua portuguesa a
                            todas as comunidades lusófonas.
                        </p>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-[#77c159]/10 to-[#77c159]/5 rounded-3xl p-8 md:p-10 flex items-center justify-center">
                    <img src="https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/user_69a0952abe7cb8c0c94a27c4/13aba247d_logo.png"
                        alt="Espaço Islâmico" class="w-48 md:w-64 opacity-90">
                </div>
            </div>
        </section>

        {{-- Values --}}
        @php
            $values = [
                [
                    'icon' => 'target',
                    'title' => 'Missão',
                    'description' =>
                        'Servir como ponte de informação e educação para a comunidade muçulmana de Moçambique e dos países lusófonos, promovendo o conhecimento islâmico autêntico.',
                ],
                [
                    'icon' => 'heart',
                    'title' => 'Valores',
                    'description' =>
                        'Verdade, integridade jornalística, respeito pela diversidade e compromisso com a educação islâmica de qualidade acessível a todos.',
                ],
                [
                    'icon' => 'globe',
                    'title' => 'Visão',
                    'description' =>
                        'Ser a principal referência em conteúdo islâmico de qualidade em língua portuguesa, conectando muçulmanos de Moçambique, Brasil, Portugal e todos os PALOP.',
                ],
            ];
        @endphp

        <section class="bg-gray-50 py-16 md:py-20">
            <div class="max-w-5xl mx-auto px-4 sm:px-6">
                <h2 class="text-3xl font-bold text-[#1a1a1a] text-center mb-12">Os Nossos Pilares</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach ($values as $index => $value)
                        <div x-data="{ show: true }" x-init="$nextTick(() => {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        show = true;
                                    }
                                });
                            }, { threshold: 0.1 });
                            observer.observe($el);
                        })" x-show="show"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="hidden"
                            class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">

                            <div class="w-12 h-12 rounded-xl bg-[#77c159]/10 flex items-center justify-center mb-4">
                                @if ($value['icon'] === 'target')
                                    <svg class="w-6 h-6 text-[#77c159]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                @elseif($value['icon'] === 'heart')
                                    <svg class="w-6 h-6 text-[#77c159]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                        </path>
                                    </svg>
                                @elseif($value['icon'] === 'globe')
                                    <svg class="w-6 h-6 text-[#77c159]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold text-[#1a1a1a] mb-3">{{ $value['title'] }}</h3>
                            <p class="text-gray-600 leading-relaxed text-sm">{{ $value['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="py-16 md:py-20">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
                <h2 class="text-3xl font-bold text-[#1a1a1a] mb-4">Junte-se à Comunidade</h2>
                <p class="text-gray-600 text-lg mb-8">
                    Siga-nos nas redes sociais e fique a par de todo o conteúdo que publicamos.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="https://www.facebook.com/profile.php?id=61570540160741" target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full transition-colors text-base">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Facebook
                    </a>
                    <a href="{{ route('articles.index') }}"
                        class="inline-flex items-center px-8 py-4 border-2 border-[#77c159] text-[#77c159] hover:bg-[#77c159]/5 font-semibold rounded-full transition-colors text-base">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        Ler Artigos
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
