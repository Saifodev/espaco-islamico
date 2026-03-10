{{-- resources/views/components/home/news-ticker.blade.php --}}
@props(['news' => []])

@php
    $defaultItems = [];
    
    $items = $news ?: $defaultItems;
@endphp

<div x-data="newsTicker()" x-init="init()" class="bg-[#1a1a1a] py-10 md:py-14 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        {{-- Section Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="w-1 h-6 bg-[#77c159] rounded-full"></div>
                <h2 class="text-white font-bold text-lg uppercase tracking-wide">Últimas Notícias</h2>
            </div>
            <a href="https://www.facebook.com/profile.php?id=61570540160741"
               target="_blank"
               rel="noopener noreferrer"
               class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-[#77c159] transition-colors">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                </svg>
                Ver no Facebook
            </a>
        </div>

        {{-- Slider --}}
        <div class="relative">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0 rounded-2xl overflow-hidden bg-[#111] shadow-2xl min-h-[320px] md:min-h-[380px]">
                {{-- Image --}}
                <div class="relative overflow-hidden">
                    <template x-if="currentItem">
                        <img :src="currentItem.image"
                             :alt="currentItem.title"
                             x-show="showImage"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 translate-x-8"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             x-transition:leave="transition ease-in duration-500"
                             x-transition:leave-start="opacity-100 translate-x-0"
                             x-transition:leave-end="opacity-0 -translate-x-8"
                             class="w-full h-full object-cover min-h-[220px] md:min-h-[380px]">
                    </template>
                    {{-- Overlay gradient --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent md:hidden"></div>
                </div>

                {{-- Content --}}
                <div class="flex flex-col justify-center p-6 md:p-10">
                    <template x-if="currentItem">
                        <div x-show="showContent"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 translate-x-8"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             x-transition:leave="transition ease-in duration-500"
                             x-transition:leave-start="opacity-100 translate-x-0"
                             x-transition:leave-end="opacity-0 -translate-x-8">
                            
                            <p class="text-[#77c159] text-xs font-semibold uppercase tracking-widest mb-3" x-text="currentItem.date"></p>
                            <h3 class="text-white text-xl md:text-2xl font-bold leading-snug mb-6" x-text="currentItem.title"></h3>
                            <div class="w-12 h-0.5 bg-[#77c159] rounded-full"></div>
                        </div>
                    </template>

                    {{-- Controls --}}
                    <div class="flex items-center gap-4 mt-8">
                        <button @click="prev()" class="p-2 rounded-full border border-white/20 text-white/60 hover:text-white hover:border-white/50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="flex gap-2">
                            <template x-for="(_, i) in items" :key="i">
                                <button @click="goTo(i)"
                                        class="h-1.5 rounded-full transition-all duration-300"
                                        :class="i === currentIndex ? 'w-6 bg-[#77c159]' : 'w-1.5 bg-white/30'"></button>
                            </template>
                        </div>
                        <button @click="next()" class="p-2 rounded-full border border-white/20 text-white/60 hover:text-white hover:border-white/50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function newsTicker() {
        return {
            items: {{ Js::from($items) }},
            currentIndex: 0,
            currentItem: null,
            showImage: true,
            showContent: true,
            interval: null,
            
            init() {
                this.currentItem = this.items[0];
                this.startAutoPlay();
            },
            
            startAutoPlay() {
                this.interval = setInterval(() => {
                    this.next();
                }, 5000);
            },
            
            stopAutoPlay() {
                if (this.interval) {
                    clearInterval(this.interval);
                }
            },
            
            async goTo(index) {
                if (index === this.currentIndex) return;
                
                this.stopAutoPlay();
                
                // Fade out
                this.showImage = false;
                this.showContent = false;
                
                await new Promise(resolve => setTimeout(resolve, 300));
                
                // Change item
                this.currentIndex = index;
                this.currentItem = this.items[index];
                
                // Fade in
                this.showImage = true;
                this.showContent = true;
                
                this.startAutoPlay();
            },
            
            next() {
                const nextIndex = (this.currentIndex + 1) % this.items.length;
                this.goTo(nextIndex);
            },
            
            prev() {
                const prevIndex = (this.currentIndex - 1 + this.items.length) % this.items.length;
                this.goTo(prevIndex);
            }
        }
    }
</script>