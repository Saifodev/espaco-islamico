{{-- resources/views/livewire/admin/article-show.blade.php --}}
<div class="min-h-screen bg-gray-50 py-6"
     x-data="{ 
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        activeTab: 'comments'
     }">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header com navegação --}}
        <div class="mb-6">
            <div class="flex items-center text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.articles.index') }}" class="hover:text-green-600">
                    Artigos
                </a>
                <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-700">{{ $article->title }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        {{ $article->title }}
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($article->status->value === 'published') bg-green-100 text-green-800
                            @elseif($article->status->value === 'draft') bg-gray-100 text-gray-800
                            @elseif($article->status->value === 'scheduled') bg-yellow-100 text-yellow-800
                            @elseif($article->status->value === 'archived') bg-red-100 text-red-800
                            @endif">
                            {{ $article->status_label }}
                        </span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Visualizando detalhes e gerenciando comentários
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.articles.edit', $article) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Artigo
                    </a>
                    @if($article->type->value === 'article')
                    <a href="{{ $article->url }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Visualizar
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Comentários</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_comments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Aprovados</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['approved_comments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pendentes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_comments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20V4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Spam</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['spam_comments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Visualizações</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['views']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs de Navegação --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button @click="activeTab = 'comments'" 
                        :class="{ 'border-green-500 text-green-600': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'comments' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Comentários
                    @if($stats['pending_comments'] > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $stats['pending_comments'] }} pendente(s)
                        </span>
                    @endif
                </button>
                <button @click="activeTab = 'details'" 
                        :class="{ 'border-green-500 text-green-600': activeTab === 'details', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'details' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Detalhes do Artigo
                </button>
            </nav>
        </div>

        {{-- Tab de Comentários --}}
        <div x-show="activeTab === 'comments'" x-transition:enter="transition ease-out duration-200">
            
            {{-- Filtros de Comentários --}}
            {{-- <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text"
                                   wire:model.live.debounce.300ms="commentSearch"
                                   placeholder="Buscar por nome, email ou conteúdo..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        <select wire:model.live="commentStatus"
                                class="block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                            <option value="all">Todos os status</option>
                            <option value="approved">Aprovados</option>
                            <option value="pending">Pendentes</option>
                            <option value="spam">Spam</option>
                        </select>
                        @if($hasActiveFilters)
                            <button type="button"
                                    wire:click="clearFilters"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Limpar
                            </button>
                        @endif
                    </div>
                </div>
            </div> --}}

            {{-- Bulk Actions Bar --}}
            @if(!empty($selectedComments))
            <div class="bg-green-50 rounded-lg shadow-sm mb-6 p-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-green-800">
                            {{ count($selectedComments) }} comentário(s) selecionado(s)
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button"
                                wire:click="confirmBulkAction('approve')"
                                class="inline-flex items-center px-3 py-1.5 border border-green-300 rounded-md text-sm font-medium text-green-700 bg-white hover:bg-green-50">
                            <svg class="-ml-1 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Aprovar
                        </button>
                        <button type="button"
                                wire:click="confirmBulkAction('spam')"
                                class="inline-flex items-center px-3 py-1.5 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-white hover:bg-yellow-50">
                            <svg class="-ml-1 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20V4"></path>
                            </svg>
                            Marcar como Spam
                        </button>
                        <button type="button"
                                wire:click="confirmBulkAction('delete')"
                                class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                            <svg class="-ml-1 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Mover para Lixeira
                        </button>
                        <button type="button"
                                wire:click="$set('selectedComments', [])"
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- Lista de Comentários --}}
            <div class="space-y-4">
                @forelse($comments as $comment)
                    {{-- @include('livewire.admin.partials.comment-thread', ['comment' => $comment]) --}}
                    <x-comment-thread :comment="$comment" :replying-to-id="$replyingToId" :reply-content="$replyContent" :editing-comment-id="$editingCommentId" />
                @empty
                    <div class="bg-white shadow-sm rounded-lg p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum comentário encontrado</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Nenhum comentário corresponde aos filtros selecionados.
                        </p>
                    </div>
                @endforelse

                {{-- Paginação --}}
                @if($comments->hasPages())
                <div class="mt-4">
                    {{ $comments->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Tab de Detalhes do Artigo --}}
        <div x-show="activeTab === 'details'" x-transition:enter="transition ease-out duration-200" class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6">
                {{-- Informações básicas --}}
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Título</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->title }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->slug }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->type_label }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Edição</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->edition ?: '-' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Autor</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->author?->name ?? 'Sistema' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tempo de leitura</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->reading_time_in_minutes }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->created_at->format('d/m/Y H:i') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Última atualização</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>

                    @if($article->published_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Publicado em</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->published_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>

                {{-- Categorias e Tags --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Categorias</h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($article->categories as $category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-500">Sem categorias</span>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($article->tags as $tag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        #{{ $tag->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-500">Sem tags</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Excerto --}}
                @if($article->excerpt)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Excerto</h3>
                    <p class="text-sm text-gray-900">{{ $article->excerpt }}</p>
                </div>
                @endif

                {{-- Conteúdo --}}
                @if($article->type->value === 'article' && $article->content)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Conteúdo</h3>
                    <div class="prose prose-sm max-w-none text-gray-900">
                        {!! $article->content !!}
                    </div>
                </div>
                @endif

                {{-- YouTube Video --}}
                @if($article->type->value === 'video' && $article->youtube_url)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Vídeo</h3>
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe src="https://www.youtube.com/embed/{{ $article->youtube_id }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                class="w-full h-full rounded-lg"></iframe>
                    </div>
                </div>
                @endif

                {{-- Imagem de destaque --}}
                @if($article->featured_image)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Imagem de destaque</h3>
                    <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="max-w-full h-auto rounded-lg shadow-sm">
                </div>
                @endif

                {{-- SEO --}}
                @if($article->seo_title || $article->seo_description || $article->seo_keywords)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">SEO</h3>
                    <dl class="space-y-3">
                        @if($article->seo_title)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Título SEO</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->seo_title }}</dd>
                        </div>
                        @endif

                        @if($article->seo_description)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Descrição SEO</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->seo_description }}</dd>
                        </div>
                        @endif

                        @if($article->seo_keywords)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Palavras-chave SEO</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->seo_keywords }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de Confirmação para Ações em Massa --}}
    @if($confirmingBulkAction)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 wire:click="$set('confirmingBulkAction', false)"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10
                            @if($bulkActionType === 'approve') bg-green-100
                            @elseif($bulkActionType === 'spam') bg-yellow-100
                            @elseif($bulkActionType === 'delete') bg-red-100
                            @endif">
                            
                            @if($bulkActionType === 'approve')
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @elseif($bulkActionType === 'spam')
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20V4"></path>
                                </svg>
                            @elseif($bulkActionType === 'delete')
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                @if($bulkActionType === 'approve')
                                    Aprovar Comentários
                                @elseif($bulkActionType === 'spam')
                                    Marcar como Spam
                                @elseif($bulkActionType === 'delete')
                                    Mover para Lixeira
                                @endif
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Tem certeza que deseja aplicar esta ação em {{ count($selectedComments) }} comentário(s)?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            wire:click="executeBulkAction"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white sm:ml-3 sm:w-auto sm:text-sm
                                @if($bulkActionType === 'approve') bg-green-600 hover:bg-green-700
                                @elseif($bulkActionType === 'spam') bg-yellow-600 hover:bg-yellow-700
                                @elseif($bulkActionType === 'delete') bg-red-600 hover:bg-red-700
                                @endif">
                        Confirmar
                    </button>
                    <button type="button"
                            wire:click="$set('confirmingBulkAction', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Toast Notification --}}
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-4 right-4 z-50"
         style="display: none;">
        <div :class="{
            'bg-green-500': toastType === 'success',
            'bg-red-500': toastType === 'error',
            'bg-yellow-500': toastType === 'warning',
            'bg-blue-500': toastType === 'info'
        }" class="text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
            <span x-text="toastMessage"></span>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                window.dispatchEvent(new CustomEvent('show-toast', { 
                    detail: { 
                        message: data.message, 
                        type: data.type 
                    } 
                }));
            });
        });

        window.addEventListener('show-toast', (event) => {
            const alpineData = document.querySelector('[x-data]').__x.$data;
            if (alpineData) {
                alpineData.toastMessage = event.detail.message;
                alpineData.toastType = event.detail.type;
                alpineData.showToast = true;
                
                setTimeout(() => {
                    alpineData.showToast = false;
                }, 5000);
            }
        });
    </script>
    @endpush
</div>