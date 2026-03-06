<div x-data="magazineReader(magazine)" x-init="init()" class="fixed inset-0 z-50 bg-[#1a1a1a] flex flex-col">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between px-4 py-3 bg-[#111] border-b border-white/10">

        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-[#77c159]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1" />
            </svg>

            <div>
                <p class="text-white text-sm font-semibold" x-text="magazine.title"></p>
                <p class="text-gray-400 text-xs" x-show="magazine.edition">
                    Edição <span x-text="magazine.edition"></span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">

            {{-- zoom --}}
            <div class="hidden sm:flex items-center gap-1 bg-white/10 rounded-full px-2 py-1">
                <button @click="zoomOut()" class="p-1 text-white/70 hover:text-white">
                    −
                </button>

                <span class="text-white text-xs px-2 min-w-[3rem] text-center"
                    x-text="Math.round(scale*100)+'%'"></span>

                <button @click="zoomIn()" class="p-1 text-white/70 hover:text-white">
                    +
                </button>
            </div>

            <a :href="magazine.pdf_url" target="_blank" download
                class="flex items-center gap-1.5 text-xs bg-[#77c159] text-white px-3 py-1.5 rounded-full">
                Download
            </a>

            <button @click="$dispatch('open-magazine', null)" class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-full">
                ✕
            </button>

        </div>
    </div>

    {{-- Viewer --}}
    <div class="flex-1 overflow-auto flex items-start justify-center py-6 px-4">

        <div x-show="loading" class="flex flex-col items-center justify-center gap-3">

            <div class="w-8 h-8 border-4 border-[#77c159] border-t-transparent rounded-full animate-spin"></div>
            <p class="text-gray-400 text-sm">A carregar jornal...</p>

        </div>

        <canvas x-ref="canvas" class="rounded-lg shadow-2xl" x-show="!loading"></canvas>

    </div>

    {{-- Navigation --}}
    <div x-show="numPages" class="flex items-center justify-center gap-4 py-4 bg-[#111] border-t border-white/10">

        <button @click="prevPage()" :disabled="currentPage <= 1"
            class="p-2 bg-white/10 text-white rounded-full disabled:opacity-30">
            ◀
        </button>

        <div class="text-white text-sm">
            <span class="font-semibold" x-text="currentPage"></span>
            <span class="text-gray-400">de</span>
            <span class="text-gray-400" x-text="numPages"></span>
        </div>

        <button @click="nextPage()" :disabled="currentPage >= numPages"
            class="p-2 bg-white/10 text-white rounded-full disabled:opacity-30">
            ▶
        </button>

    </div>

</div>