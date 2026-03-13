<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>@yield('title', config('app.name', 'Espaço Islâmico'))</title>
    <meta name="description" content="@yield('description', 'Um espaço dedicado ao mundo Islâmico — Moçambique & PALOP')">
    <meta name="keywords" content="@yield('keywords', 'islão, islâmico, moçambique, palop, jornal, notícias')">

    {{-- Open Graph / Social Media --}}
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ View::yieldContent('og_title', View::yieldContent('title')) }}">
    <meta property="og:description"
        content="{{ View::yieldContent('og_description', View::yieldContent('description')) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- Styles --}}
    @vite(['resources/css/app.css'])
    @livewireStyles
    @stack('styles')

    {{-- Custom CSS Variables --}}
    <style>
        :root {
            --brand-green: #77c159;
            --brand-green-dark: #5fa343;
            --brand-green-light: #e8f5e0;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="font-sans antialiased bg-white text-gray-900">
    <div class="min-h-screen flex flex-col">
        {{-- Top Bar --}}
        <div class="bg-[#5fa343] text-white/80 text-xs py-2 px-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <span>Um espaço dedicado ao mundo Islâmico</span>
                <div class="hidden sm:flex items-center gap-4">
                    <a href="https://www.facebook.com/profile.php?id=61570540160741" target="_blank"
                        rel="noopener noreferrer">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <a href="mailto:info@espacoislamico.com">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Header --}}
        <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                @php
                    $navLinks = [
                        ['label' => 'Início', 'route' => 'home'],
                        ['label' => 'Notícias', 'route' => 'articles.type', 'type' => 'news'],
                        ['label' => 'Artigos', 'route' => 'articles.index', 'type' => 'article'],
                        ['label' => 'Vídeos', 'route' => 'articles.type', 'type' => 'video'],
                        ['label' => 'Jornais', 'route' => 'articles.type', 'type' => 'newspaper'],
                        ['label' => 'Sobre Nós', 'route' => 'about'],
                    ];
                    $currentRoute = Route::currentRouteName();
                    $currentType = request()->route('type');
                @endphp

                {{-- Desktop Layout --}}
                <div class="hidden md:flex items-center justify-between h-20">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                        <img src="https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/user_69a0952abe7cb8c0c94a27c4/13aba247d_logo.png"
                            alt="Espaço Islâmico" class="h-12 w-auto">
                        <div>
                            <h1 class="text-lg font-bold text-[#1a1a1a] leading-tight tracking-tight">Espaço Islâmico
                            </h1>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest">Jornal Digital</p>
                        </div>
                    </a>

                    <nav class="flex items-center gap-1">
                        @foreach ($navLinks as $link)
                            @php
                                $isActive =
                                    $link['route'] === 'home'
                                        ? $currentRoute === 'home'
                                        : ($link['route'] === 'articles.type'
                                            ? $currentType === $link['type']
                                            : $currentRoute === $link['route']);
                            @endphp

                            @if ($link['route'] === 'articles.type')
                                <a href="{{ route('articles.type', ['type' => $link['type']]) }}"
                                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 
                                          {{ $isActive
                                              ? 'text-[var(--brand-green)] bg-[var(--brand-green-light)]'
                                              : 'text-gray-700 hover:text-[var(--brand-green)] hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                </a>
                            @else
                                <a href="{{ route($link['route']) }}"
                                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 
                                          {{ $isActive
                                              ? 'text-[var(--brand-green)] bg-[var(--brand-green-light)]'
                                              : 'text-gray-700 hover:text-[var(--brand-green)] hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </nav>
                </div>

                {{-- Mobile Layout --}}
                <div class="md:hidden py-3">
                    <nav class="flex items-center justify-between gap-1 overflow-x-auto pb-1 scrollbar-hide">
                        @foreach ($navLinks as $link)
                            @php
                                $isActive =
                                    $link['route'] === 'home'
                                        ? $currentRoute === 'home'
                                        : ($link['route'] === 'articles.type'
                                            ? $currentType === $link['type']
                                            : $currentRoute === $link['route']);
                            @endphp

                            @if ($link['route'] === 'articles.type')
                                <a href="{{ route('articles.type', ['type' => $link['type']]) }}"
                                    class="px-3 py-2 text-xs font-medium whitespace-nowrap rounded-lg transition-all duration-200
                                          {{ $isActive
                                              ? 'text-[var(--brand-green)] bg-[var(--brand-green-light)]'
                                              : 'text-gray-700 hover:text-[var(--brand-green)] hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                </a>
                            @else
                                <a href="{{ route($link['route']) }}"
                                    class="px-3 py-2 text-xs font-medium whitespace-nowrap rounded-lg transition-all duration-200
                                          {{ $isActive
                                              ? 'text-[var(--brand-green)] bg-[var(--brand-green-light)]'
                                              : 'text-gray-700 hover:text-[var(--brand-green)] hover:bg-gray-50' }}">
                                    {{ $link['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </nav>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="bg-[#1a1a1a] text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 md:py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    {{-- About --}}
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <img src="https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/user_69a0952abe7cb8c0c94a27c4/13aba247d_logo.png"
                                alt="Espaço Islâmico" class="h-12 w-auto">
                            <div>
                                <h3 class="text-lg font-bold">Espaço Islâmico</h3>
                                <p class="text-xs text-gray-400">Jornal Digital</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 leading-relaxed max-w-md">
                            Criado no início de 2025 na província de Nampula, com sede actual na Cidade de Maputo,
                            o Jornal Espaço Islâmico nasceu com a missão de servir como ponte de informação e educação
                            para a comunidade muçulmana de Moçambique e dos países lusófonos.
                        </p>
                    </div>

                    {{-- Navigation --}}
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wider text-[var(--brand-green)] mb-4">
                            Navegação</h4>
                        <ul class="space-y-2.5">
                            @foreach ($navLinks as $link)
                                <li>
                                    @if ($link['route'] === 'articles.type')
                                        <a href="{{ route('articles.type', ['type' => $link['type']]) }}"
                                            class="text-sm text-gray-400 hover:text-white transition-colors">
                                            {{ $link['label'] }}
                                        </a>
                                    @else
                                        <a href="{{ route($link['route']) }}"
                                            class="text-sm text-gray-400 hover:text-white transition-colors">
                                            {{ $link['label'] }}
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Contact --}}
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wider text-[var(--brand-green)] mb-4">
                            Contacto</h4>
                        <ul class="space-y-2.5 text-sm text-gray-400">
                            <li>Maputo, Moçambique</li>
                            <li>
                                <a href="https://www.facebook.com/profile.php?id=61570540160741" target="_blank"
                                    rel="noopener noreferrer"
                                    class="hover:text-white transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Facebook
                                </a>
                            </li>
                            <li>
                                <a href="mailto:info@espacoislamico.com"
                                    class="hover:text-white transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Email
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Bottom Bar --}}
                <div
                    class="border-t border-white/10 mt-10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-gray-500">© {{ date('Y') }} Espaço Islâmico. Todos os direitos
                        reservados.</p>
                    <p class="text-xs text-gray-500">Moçambique & PALOP</p>
                </div>
            </div>
        </footer>
    </div>

    {{-- Scripts --}}
    @vite(['resources/js/app.js'])
    @livewireScripts

    {{-- PDF.js --}}
    <script src="https://unpkg.com/pdfjs-dist@3.10.111/legacy/build/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            "https://unpkg.com/pdfjs-dist@3.10.111/legacy/build/pdf.worker.min.js";
    </script>

    {{-- Alpine.js e outras funcionalidades --}}
    @stack('scripts')
</body>

</html>
