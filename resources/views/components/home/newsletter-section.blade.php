{{-- resources/views/components/home/newsletter-section.blade.php --}}
<div x-data="newsletter()" class="relative bg-gradient-to-br from-[#77c159] to-[#5fa343] py-16 md:py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M20 20.5V18H0v-2h20v-2l2.5 3.5L20 21z\' fill=\'%23000\' fill-opacity=\'.15\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
    
    <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <div x-data="{ show: false }" 
             x-init="setTimeout(() => show = true, 100)"
             x-show="show"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-3">
                Fique Ligado
            </h2>
            <p class="text-white/80 text-lg mb-8">
                Receba os nossos artigos e vídeos directamente no seu email.
            </p>

            <template x-if="submitted">
                <div class="flex items-center justify-center gap-3 bg-white/20 backdrop-blur-sm rounded-full px-6 py-4 mx-auto max-w-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-white font-medium">Subscrito com sucesso!</span>
                </div>
            </template>

            <template x-if="!submitted">
                <form @submit.prevent="submitNewsletter" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
                    <input type="email"
                           x-model="email"
                           required
                           placeholder="O seu email"
                           class="flex-1 rounded-full px-6 py-3 bg-white/95 border-0 text-gray-800 placeholder:text-gray-400 shadow-lg focus:outline-none focus:ring-2 focus:ring-white/50">
                    <button type="submit"
                            :disabled="loading"
                            class="bg-[#1a1a1a] hover:bg-black text-white rounded-full px-8 py-3 font-semibold shadow-lg transition-all disabled:opacity-50">
                        <span x-show="!loading" class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Subscrever
                        </span>
                        <span x-show="loading">A enviar...</span>
                    </button>
                </form>
            </template>
        </div>
    </div>
</div>

<script>
    function newsletter() {
        return {
            email: '',
            submitted: false,
            loading: false,
            
            async submitNewsletter() {
                if (!this.email) return;
                
                this.loading = true;
                
                try {
                    // Aqui você deve implementar a lógica de subscrição
                    // Exemplo com fetch para uma rota Laravel
                    const response = await fetch('{{ route("newsletter.subscribe") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ email: this.email })
                    });
                    
                    if (response.ok) {
                        this.submitted = true;
                        this.email = '';
                        
                        // Reset após 5 segundos
                        setTimeout(() => {
                            this.submitted = false;
                        }, 5000);
                    }
                } catch (error) {
                    console.error('Erro ao subscrever:', error);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>