<div class="fixed inset-0 z-50 bg-[#1a1a1a] flex flex-col">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between px-4 py-3 bg-[#111] border-b border-white/10 shrink-0">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-[#77c159]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
            <div>
                <p class="text-white font-semibold text-sm"><span x-text="magazine.title"></span></p>
                {{-- @if($item?->edition)
                    <p class="text-gray-400 text-xs">Edição <span x-text="$root.magazine.edition"></span></p>
                @endif --}}
                <span x-text="magazine.edition"></span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Zoom controls --}}
            <div class="hidden sm:flex items-center gap-1 bg-white/10 rounded-full px-2 py-1">
                <button @click="zoomOut" class="p-1 text-white/70 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                    </svg>
                </button>
                <span class="text-white text-xs px-2 min-w-[3rem] text-center" x-text="Math.round(scale * 100) + '%'"></span>
                <button @click="zoomIn" class="p-1 text-white/70 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                    </svg>
                </button>
            </div>

            <a href="#" target="_blank"
               class="flex items-center gap-1.5 text-xs bg-[#77c159] text-white px-3 py-1.5 rounded-full hover:bg-[#5fa343] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span class="hidden sm:inline">Download</span>
            </a>

            <button @click="close()" class="p-2 rounded-full text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- PDF Viewer --}}
    <div class="flex-1 overflow-auto flex items-start justify-center py-6 px-4">
        <template x-if="loading">
            <div class="flex flex-col items-center justify-center py-20 gap-3">
                <svg class="w-8 h-8 animate-spin text-[#77c159]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-400 text-sm">A carregar jornal...</p>
            </div>
        </template>

        {{-- PDF.js would be integrated here --}}
        {{-- For now, using iframe as fallback --}}
        <iframe :src="magazine.pdf_url"
                class="w-full h-full rounded-lg shadow-2xl"
                frameborder="0">
        </iframe>
    </div>

    {{-- Page Navigation --}}
    <div class="shrink-0 flex items-center justify-center gap-4 py-4 bg-[#111] border-t border-white/10">
        <button @click="prevPage"
                :disabled="currentPage <= 1"
                class="p-2 rounded-full bg-white/10 text-white disabled:opacity-30 hover:bg-white/20 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <div class="flex items-center gap-2 text-white text-sm">
            <span class="font-semibold" x-text="currentPage"></span>
            <span class="text-gray-400">de</span>
            <span class="text-gray-400" x-text="numPages || '?'"></span>
        </div>

        <button @click="nextPage"
                :disabled="currentPage >= (numPages || 0)"
                class="p-2 rounded-full bg-white/10 text-white disabled:opacity-30 hover:bg-white/20 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</div>