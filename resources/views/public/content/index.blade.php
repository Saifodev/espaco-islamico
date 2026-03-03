@extends('layouts.public')

@section('title', $pageTitle . ' - Espaço Islâmico')
@section('description', $typeData['description'])

@push('styles')
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endpush

@section('content')
    <div x-data="magazineModal()" x-on:open-magazine.window="open($event.detail)" class="relative">
        <div class="min-h-screen bg-white">
            {{-- Header --}}
            <div class="bg-gradient-to-br from-[#1a1a1a] to-[#2d2d2d] py-16 md:py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
                    @if ($type === 'newspaper' && isset($typeData['badge']))
                        <div
                            class="inline-flex items-center gap-2 bg-[#77c159]/10 border border-[#77c159]/20 rounded-full px-4 py-1.5 mb-4">
                            <svg class="w-4 h-4 text-[#77c159]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                </path>
                            </svg>
                            <span
                                class="text-[#77c159] text-xs font-medium uppercase tracking-wider">{{ $typeData['badge'] }}</span>
                        </div>
                    @endif

                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ $pageTitle }}</h1>
                    <p class="text-gray-400 text-lg max-w-2xl mx-auto {{ $type === 'article' ? 'mb-8' : '' }}">
                        {{ $typeData['description'] }}
                    </p>

                    {{-- Search only for articles --}}
                    @if ($type === 'article')
                        <div class="max-w-md mx-auto relative">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" placeholder="Pesquisar artigos..." value="{{ $searchQuery }}"
                                class="w-full pl-12 py-4 rounded-full bg-white/10 border-white/20 text-white placeholder:text-gray-400 focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-[#77c159]/50"
                                x-data
                                x-on:keyup="window.location.href = '{{ route('articles.type', $type) }}?search=' + encodeURIComponent($el.value)">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Filters / Categories --}}
            @if (count($categories) > 0 && $type !== 'newspaper')
                <div class="border-b border-gray-100 sticky top-16 md:top-20 bg-white/95 backdrop-blur-md z-30">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6">
                        <div class="flex items-center gap-2 py-4 overflow-x-auto scrollbar-hide">
                            @foreach ($categories as $cat)
                                <a href="{{ route('articles.type', $type) }}?category={{ $cat['id'] }}"
                                    class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all {{ (string) $selectedCategory === (string) $cat['id']
                                        ? 'bg-[#77c159] text-white shadow-md shadow-[#77c159]/20'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $cat['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Content Grid --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 md:py-16">
                @if ($items->count() > 0)
                    @if ($type === 'newspaper')
                        {{-- Newspaper Grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                            @foreach ($items as $index => $item)
                                <x-content-card :item="$item" :type="$type" :index="$index" />
                            @endforeach
                        </div>
                    @elseif($type === 'video')
                        {{-- Video Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($items as $index => $item)
                                <x-content-card :item="$item" :type="$type" :index="$index" />
                            @endforeach
                        </div>
                    @else
                        {{-- Article Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($items as $index => $item)
                                <x-content-card :item="$item" :type="$type" :index="$index" />
                            @endforeach
                        </div>
                    @endif

                    {{-- Pagination --}}
                    <div class="mt-12">
                        {{ $items->withQueryString()->links() }}
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-20">
                        @php
                            $icon = $typeData['icon'] ?? 'file-text';
                        @endphp
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            @if ($icon === 'book-open')
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            @elseif($icon === 'play')
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                    </path>
                                </svg>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700">{{ $typeData['empty_message'] }}</h3>
                        <p class="text-gray-500 mt-1">{{ $typeData['empty_submessage'] }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Magazine Modal --}}
        <template x-if="magazine">
            <div class="fixed inset-0 z-50">
                <x-magazine-reader />
            </div>
        </template>
    </div>

    {{-- Video Modal (only for videos) --}}
    @if ($type === 'video')
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
    @endif
@endsection

@push('scripts')
    <script>

        function magazineModal() {
            return {
                magazine: null,

                open(data) {
                    this.magazine = data
                    document.body.classList.add('overflow-hidden')
                    console.log('Abrindo revista:', data)
                },

                close() {
                    this.magazine = null
                    document.body.classList.remove('overflow-hidden')
                }
            }
        }
    </script>
@endpush
