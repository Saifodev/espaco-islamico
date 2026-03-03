{{-- resources/views/components/comment-section.blade.php --}}
@props(['articleId'])

<div 
    x-data="{
        comments: [],
        count_comments: 0,
        name: '',
        email: '',
        content: '',
        replyTo: null,
        replyToName: '',
        submitted: false,
        loading: false,
        error: '',

        loadComments() {
            if (!'{{ $articleId }}') return;

            fetch(`/comments/article/{{ $articleId }}`)
                .then(r => r.json())
                .then(data => {
                    this.comments = data.data || [];
                    this.count_comments = data.count_comments || 0;
                })
                .catch(e => console.error(e));
        },

        setReplyTo(commentId, authorName) {
            this.replyTo = commentId;
            this.replyToName = authorName;
            // Scroll to form
            this.$nextTick(() => {
                document.querySelector('.comment-form').scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        },

        cancelReply() {
            this.replyTo = null;
            this.replyToName = '';
        },

        submitComment() {
            if (!this.name || !this.content) return;

            this.loading = true;
            this.error = '';

            const payload = {
                article_id: '{{ $articleId }}',
                name: this.name,
                email: this.email,
                content: this.content
            };

            // Add parent_id if replying to a comment
            if (this.replyTo) {
                payload.parent_id = this.replyTo;
            }

            fetch('/comments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.submitted = true;
                    this.content = '';
                    this.cancelReply(); // Clear reply state
                    this.loadComments();

                    setTimeout(() => { 
                        this.submitted = false; 
                        this.name = ''; 
                        this.email = ''; 
                    }, 5000);
                } else {
                    this.error = data.message || 'Erro ao enviar comentário.';
                }
            })
            .catch(e => { this.error = 'Erro de conexão'; console.error(e); })
            .finally(() => this.loading = false);
        },

        // Helper to check if a comment has replies
        hasReplies(comment) {
            return comment.replies && comment.replies.length > 0;
        }
    }"
    x-init="loadComments()"
    class="mt-12 pt-10 border-t border-gray-100">

    {{-- Error message --}}
    <template x-if="error">
        <div class="mb-4 p-4 bg-red-50 text-red-600 rounded-xl text-sm" x-text="error"></div>
    </template>

    {{-- Comment Form --}}
    <div class="bg-gray-50 rounded-2xl p-6 mb-10 comment-form">
        <h4 class="text-lg font-semibold text-[#1a1a1a] mb-4">
            Deixe o seu comentário
            <template x-if="replyToName">
                <span class="text-sm font-normal text-gray-500 ml-2">
                    (Respondendo a <span class="font-medium text-[#77c159]" x-text="replyToName"></span>)
                </span>
            </template>
        </h4>
        
        {{-- Reply indicator with cancel button --}}
        <template x-if="replyToName">
            <div class="mb-4 p-3 bg-[#77c159]/5 border border-[#77c159]/20 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-[#77c159]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                    <span>A responder a <span class="font-semibold" x-text="replyToName"></span></span>
                </div>
                <button @click="cancelReply" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </template>
        
        <template x-if="submitted">
            <div class="text-center py-6"
                 x-show="submitted"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="w-12 h-12 bg-[#77c159]/10 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-[#77c159]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <p class="text-[#1a1a1a] font-semibold">Comentário enviado!</p>
                <p class="text-gray-500 text-sm mt-1">Aguardando aprovação. Obrigado!</p>
            </div>
        </template>

        <template x-if="!submitted">
            <form @submit.prevent="submitComment" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-600 mb-1.5 block">Nome *</label>
                        <input type="text"
                               x-model="name"
                               required
                               placeholder="O seu nome"
                               class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:border-[#77c159] focus:ring-1 focus:ring-[#77c159] outline-none transition">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 mb-1.5 block">Email (opcional)</label>
                        <input type="email" 
                               x-model="email"
                               placeholder="email@example.com"
                               class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:border-[#77c159] focus:ring-1 focus:ring-[#77c159] outline-none transition">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 mb-1.5 block">Comentário *</label>
                    <textarea x-model="content"
                              required
                              :placeholder="replyToName ? `Responder a ${replyToName}...` : 'Escreva o seu comentário aqui...'"
                              rows="4"
                              class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:border-[#77c159] focus:ring-1 focus:ring-[#77c159] outline-none transition resize-none"></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit"
                            :disabled="loading"
                            class="bg-[#77c159] hover:bg-[#5fa343] text-white rounded-full px-8 py-3 text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading" x-text="replyTo ? 'Publicar Resposta' : 'Publicar Comentário'"></span>
                        <span x-show="loading">A enviar...</span>
                    </button>
                    
                    {{-- Cancel reply button --}}
                    <template x-if="replyTo">
                        <button type="button" 
                                @click="cancelReply"
                                class="text-gray-400 hover:text-gray-600 text-sm transition-colors">
                            Cancelar
                        </button>
                    </template>
                </div>
            </form>
        </template>
    </div>

    {{-- Comments List --}}
    <div class="space-y-6">
        <h3 class="text-2xl font-bold text-[#1a1a1a] mb-8 flex items-center gap-3">
            <svg class="w-6 h-6 text-[#77c159]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            Comentários
            <span class="text-gray-400 text-lg" x-text="`(${count_comments})`">(0)</span>
        </h3>
        
        <template x-for="(comment, index) in comments" :key="comment.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 :style="{ transitionDelay: (index * 50) + 'ms' }">
                
                {{-- Main comment --}}
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-full bg-[#77c159]/10 flex items-center justify-center shrink-0 mt-1">
                        <svg class="w-5 h-5 text-[#77c159]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    
                    <div class="flex-1 bg-white border border-gray-100 rounded-2xl p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-[#1a1a1a]" x-text="comment.author_name"></span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span x-text="comment.created_date"></span>
                                </span>
                            </div>
                            
                            {{-- Reply button --}}
                            <button @click="setReplyTo(comment.id, comment.author_name)"
                                    class="text-xs text-gray-400 hover:text-[#77c159] transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Responder
                            </button>
                        </div>
                        
                        <p class="text-gray-600 leading-relaxed text-sm" x-text="comment.content"></p>
                    </div>
                </div>
                
                {{-- Replies section --}}
                <template x-if="comment.replies && comment.replies.length">
                    <div class="mt-4 ml-8 space-y-4 border-l-2 border-[#77c159]/20 pl-4">
                        <template x-for="reply in comment.replies" :key="reply.id">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#77c159]/5 flex items-center justify-center shrink-0 mt-1">
                                    <svg class="w-4 h-4 text-[#77c159]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                
                                <div class="flex-1 bg-gray-50 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-sm text-[#1a1a1a]" x-text="reply.author_name"></span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-400" x-text="reply.created_date"></span>
                                        </div>
                                        
                                        {{-- Reply button for nested replies (limited depth) --}}
                                        <button @click="setReplyTo(reply.id, reply.author_name)"
                                                class="text-xs text-gray-400 hover:text-[#77c159] transition-colors flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                            </svg>
                                            Responder
                                        </button>
                                    </div>
                                    <p class="text-gray-600 text-sm" x-text="reply.content"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="comments.length === 0">
            <div class="text-center py-8 text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-sm">Seja o primeiro a comentar.</p>
            </div>
        </template>
    </div>
</div>