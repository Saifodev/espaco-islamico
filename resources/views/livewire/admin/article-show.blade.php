{{-- resources/views/livewire/admin/article-show.blade.php --}}
<div class="min-h-screen bg-gray-50 py-8"
     x-data="{ 
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        activeTab: 'comments'
     }">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with navigation --}}
        <div class="mb-6">
            <div class="flex items-center text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.articles.index') }}" class="hover:text-green-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Conteúdo
                </a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <span class="text-gray-700 font-medium">{{ Str::limit($article->title, 50) }}</span>
            </div>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-serif font-semibold text-gray-800 flex items-center gap-3">
                        {{ $article->title }}
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium 
                            @if($article->status->value === 'published') bg-green-100 text-green-800
                            @elseif($article->status->value === 'draft') bg-gray-100 text-gray-800
                            @elseif($article->status->value === 'scheduled') bg-yellow-100 text-yellow-800
                            @elseif($article->status->value === 'archived') bg-red-100 text-red-800
                            @endif">
                            <i class="fas 
                                @if($article->status->value === 'published') fa-check-circle mr-1
                                @elseif($article->status->value === 'draft') fa-pen mr-1
                                @elseif($article->status->value === 'scheduled') fa-clock mr-1
                                @elseif($article->status->value === 'archived') fa-archive mr-1
                                @endif">
                            </i>
                            {{ $article->status_label }}
                        </span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Visualizando detalhes e gerenciando comentários
                    </p>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.articles.edit', $article) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-edit mr-2 text-gray-500"></i>
                        Editar
                    </a>
                    
                    @if($article->type->value === 'article')
                    <a href="{{ $article->url }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Visualizar
                    </a>
                    @endif
                    
                    <a href="{{ redirect()->back()->getTargetUrl() }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-comments text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Comentários</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['total_comments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Aprovados</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['approved_comments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Pendentes</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['pending_comments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-ban text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Spam</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['spam_comments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-eye text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Visualizações</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['views']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs de Navegação --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button @click="activeTab = 'comments'" 
                        :class="{ 'border-green-600 text-green-600': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'comments' }"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center">
                    <i class="fas fa-comments mr-2"></i>
                    Comentários
                    @if($stats['pending_comments'] > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $stats['pending_comments'] }}
                        </span>
                    @endif
                </button>
                <button @click="activeTab = 'details'" 
                        :class="{ 'border-green-600 text-green-600': activeTab === 'details', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'details' }"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-info-circle mr-2"></i>
                    Detalhes
                </button>
            </nav>
        </div>

        {{-- Tab de Comentários --}}
        <div x-show="activeTab === 'comments'" x-transition:enter="transition ease-out duration-200">
            
            {{-- Filtros de Comentários --}}
            {{-- <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                               wire:model.live.debounce.300ms="commentSearch"
                               placeholder="Buscar por nome, email ou conteúdo..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                    </div>
                    <select wire:model.live="commentStatus"
                            class="w-full sm:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                        <option value="all">Todos os status</option>
                        <option value="approved">Aprovados</option>
                        <option value="pending">Pendentes</option>
                        <option value="spam">Spam</option>
                    </select>
                    @if($hasActiveFilters)
                        <button type="button"
                                wire:click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Limpar
                        </button>
                    @endif
                </div>
            </div> --}}

            {{-- Bulk Actions Bar --}}
            @if(!empty($selectedComments))
            <div class="bg-green-50 rounded-xl border border-green-200 mb-6 p-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ count($selectedComments) }} comentário(s) selecionado(s)
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button"
                                wire:click="confirmBulkAction('approve')"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-green-300 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 transition-colors">
                            <i class="fas fa-check mr-1"></i>
                            Aprovar
                        </button>
                        <button type="button"
                                wire:click="confirmBulkAction('spam')"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-yellow-300 rounded-lg text-sm font-medium text-yellow-700 hover:bg-yellow-50 transition-colors">
                            <i class="fas fa-ban mr-1"></i>
                            Marcar como Spam
                        </button>
                        <button type="button"
                                wire:click="confirmBulkAction('delete')"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-red-300 rounded-lg text-sm font-medium text-red-700 hover:bg-red-50 transition-colors">
                            <i class="fas fa-trash mr-1"></i>
                            Mover para Lixeira
                        </button>
                        <button type="button"
                                wire:click="$set('selectedComments', [])"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-1"></i>
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- Lista de Comentários --}}
            <div class="space-y-4">
                @forelse($comments as $comment)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        {{-- Cabeçalho do comentário --}}
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">
                                        {{ strtoupper(substr($comment->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $comment->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $comment->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                    @if($comment->status === 'approved') bg-green-100 text-green-800
                                    @elseif($comment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($comment->status === 'spam') bg-red-100 text-red-800
                                    @endif">
                                    <i class="fas 
                                        @if($comment->status === 'approved') fa-check-circle mr-1
                                        @elseif($comment->status === 'pending') fa-clock mr-1
                                        @elseif($comment->status === 'spam') fa-ban mr-1
                                        @endif">
                                    </i>
                                    {{ ucfirst($comment->status) }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Conteúdo do comentário --}}
                        <div class="px-6 py-4">
                            @if($editingCommentId === $comment->id)
                                <textarea wire:model="editingContent" 
                                          rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"></textarea>
                                <div class="mt-3 flex justify-end space-x-2">
                                    <button wire:click="updateComment"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <i class="fas fa-save mr-2"></i>
                                        Salvar
                                    </button>
                                    <button wire:click="cancelEdit"
                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        Cancelar
                                    </button>
                                </div>
                            @else
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $comment->content }}</p>
                            @endif

                            {{-- Ações do comentário --}}
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox"
                                           wire:model.live="selectedComments"
                                           value="{{ $comment->id }}"
                                           class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-200">
                                    <span class="text-xs text-gray-500">Selecionar</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($comment->status !== 'approved')
                                        <button wire:click="approveComment({{ $comment->id }})"
                                                class="p-2 text-gray-500 hover:text-green-600 rounded-lg hover:bg-green-50 transition-colors"
                                                title="Aprovar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    
                                    @if($comment->status !== 'spam')
                                        <button wire:click="markAsSpam({{ $comment->id }})"
                                                class="p-2 text-gray-500 hover:text-yellow-600 rounded-lg hover:bg-yellow-50 transition-colors"
                                                title="Marcar como Spam">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @endif
                                    
                                    <button wire:click="startEdit({{ $comment->id }})"
                                            class="p-2 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                            title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <button wire:click="moveToTrash({{ $comment->id }})"
                                            class="p-2 text-gray-500 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Mover para lixeira">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <i class="fas fa-comments text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm">Nenhum comentário encontrado</p>
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

        {{-- Tab de Detalhes --}}
        <div x-show="activeTab === 'details'" x-transition:enter="transition ease-out duration-200">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Informações básicas --}}
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-green-600 mr-2"></i>
                        Informações Básicas
                    </h3>
                    
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Título</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->title }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->slug }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                    @if($article->type->value === 'video') bg-purple-100 text-purple-800
                                    @elseif($article->type->value === 'newspaper') bg-blue-100 text-blue-800
                                    @elseif($article->type->value === 'news') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    <i class="fas 
                                        @if($article->type->value === 'video') fa-video mr-1
                                        @elseif($article->type->value === 'newspaper') fa-newspaper mr-1
                                        @elseif($article->type->value === 'news') fa-bolt mr-1
                                        @else fa-file-alt mr-1
                                        @endif">
                                    </i>
                                    {{ $article->type_label }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Edição</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->edition ?: '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Autor</dt>
                            <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                <div class="h-6 w-6 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center mr-2">
                                    <span class="text-white text-xs font-medium">
                                        {{ strtoupper(substr($article->author?->name ?? 'S', 0, 1)) }}
                                    </span>
                                </div>
                                {{ $article->author?->name ?? 'Sistema' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tempo de leitura</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->reading_time_in_minutes }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->created_at->format('d/m/Y H:i') }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Última atualização</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>

                        @if($article->published_at)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Publicado em</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->published_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                {{-- Categorias e Tags --}}
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tags text-green-600 mr-2"></i>
                        Categorização
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Categorias</dt>
                            <dd>
                                @forelse($article->categories as $category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-2">
                                        <i class="fas fa-folder mr-1"></i>
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-500">Sem categorias</span>
                                @endforelse
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Tags</dt>
                            <dd>
                                @forelse($article->tags as $tag)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800 mr-2 mb-2">
                                        <i class="fas fa-hashtag mr-1"></i>
                                        {{ $tag->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-500">Sem tags</span>
                                @endforelse
                            </dd>
                        </div>
                    </div>
                </div>

                {{-- Excerto --}}
                @if($article->excerpt)
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-quote-right text-green-600 mr-2"></i>
                        Excerto
                    </h3>
                    <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $article->excerpt }}</p>
                </div>
                @endif

                {{-- Conteúdo --}}
                @if(in_array($article->type->value, ['article', 'news']) && $article->content)
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-file-alt text-green-600 mr-2"></i>
                        Conteúdo
                    </h3>
                    <div class="prose prose-sm max-w-none text-gray-700 bg-gray-50 p-4 rounded-lg">
                        {!! $article->content !!}
                    </div>
                </div>
                @endif

                {{-- Vídeo --}}
                @if($article->type->value === 'video' && $article->youtube_url)
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-video text-green-600 mr-2"></i>
                        Vídeo
                    </h3>
                    <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden">
                        <iframe src="https://www.youtube.com/embed/{{ $article->youtube_id }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                class="w-full h-full"></iframe>
                    </div>
                </div>
                @endif

                {{-- Imagem de destaque --}}
                @if($article->featured_image)
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-image text-green-600 mr-2"></i>
                        Imagem de destaque
                    </h3>
                    <img src="{{ $article->featured_image }}" 
                         alt="{{ $article->title }}" 
                         class="max-w-full h-auto rounded-lg shadow-sm max-h-96">
                </div>
                @endif

                {{-- SEO --}}
                @if($article->seo_title || $article->seo_description || $article->seo_keywords)
                <div class="p-6">
                    <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-green-600 mr-2"></i>
                        SEO
                    </h3>
                    
                    <dl class="space-y-4">
                        @if($article->seo_title)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Título SEO</dt>
                            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $article->seo_title }}</dd>
                        </div>
                        @endif

                        @if($article->seo_description)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição SEO</dt>
                            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $article->seo_description }}</dd>
                        </div>
                        @endif

                        @if($article->seo_keywords)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Palavras-chave</dt>
                            <dd class="mt-1">
                                @foreach(explode(',', $article->seo_keywords) as $keyword)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800 mr-2 mb-2">
                                        {{ trim($keyword) }}
                                    </span>
                                @endforeach
                            </dd>
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
    <div class="fixed inset-0 z-50 overflow-y-auto"
         x-data
         x-init="$el.addEventListener('click', e => { if (e.target === $el) { $wire.set('confirmingBulkAction', false); } })">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10
                            @if($bulkActionType === 'approve') bg-green-100
                            @elseif($bulkActionType === 'spam') bg-yellow-100
                            @elseif($bulkActionType === 'delete') bg-red-100
                            @endif">
                            
                            @if($bulkActionType === 'approve')
                                <i class="fas fa-check-circle text-xl text-green-600"></i>
                            @elseif($bulkActionType === 'spam')
                                <i class="fas fa-ban text-xl text-yellow-600"></i>
                            @elseif($bulkActionType === 'delete')
                                <i class="fas fa-exclamation-triangle text-xl text-red-600"></i>
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
                                    Tem certeza que deseja aplicar esta ação em <span class="font-medium">{{ count($selectedComments) }}</span> comentário(s)?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            wire:click="executeBulkAction"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white sm:ml-3 sm:w-auto
                                @if($bulkActionType === 'approve') bg-green-600 hover:bg-green-700
                                @elseif($bulkActionType === 'spam') bg-yellow-600 hover:bg-yellow-700
                                @elseif($bulkActionType === 'delete') bg-red-600 hover:bg-red-700
                                @endif">
                        <i class="fas 
                            @if($bulkActionType === 'approve') fa-check-circle mr-2
                            @elseif($bulkActionType === 'spam') fa-ban mr-2
                            @elseif($bulkActionType === 'delete') fa-trash mr-2
                            @endif">
                        </i>
                        Confirmar
                    </button>
                    <button type="button"
                            wire:click="$set('confirmingBulkAction', false)"
                            class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto">
                        <i class="fas fa-times mr-2"></i>
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
            <i :class="{
                'fas fa-check-circle mr-2': toastType === 'success',
                'fas fa-exclamation-circle mr-2': toastType === 'error',
                'fas fa-exclamation-triangle mr-2': toastType === 'warning',
                'fas fa-info-circle mr-2': toastType === 'info'
            }"></i>
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