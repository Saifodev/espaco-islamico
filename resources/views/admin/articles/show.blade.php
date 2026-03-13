{{-- resources/views/admin/articles/show.blade.php --}}
<x-admin>
    <div class="min-h-screen bg-gray-50 py-8" x-data="{
        activeTab: 'comments',
        selectedComments: [],
        selectAll: false,
        confirmingBulkAction: false,
        bulkActionType: '',
        editingCommentId: null,
        editingContent: '',
        replyingToId: null,
        replyContent: ''
    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header with navigation --}}
            <x-slot name="header">
            <div class="mb-6">
                {{-- <div class="flex items-center text-sm text-gray-500 mb-4">
                    <a href="{{ route('admin.articles.index') }}" class="hover:text-green-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Conteúdo
                    </a>
                    <i class="fas fa-chevron-right mx-2 text-xs"></i>
                    <span class="text-gray-700 font-medium">{{ Str::limit($article->title, 50) }}</span>
                </div> --}}

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-serif font-semibold text-gray-800 flex items-center gap-3">
                            {{ $article->title }}
                            {{-- @include('admin.articles.partials.status-badge', [
                                'status' => $article->status,
                            ]) --}}
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

                        @if ($article->type->value === 'article')
                            <a href="{{ $article->url }}" target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Visualizar
                            </a>
                        @endif

                        <a href="{{ route('admin.articles.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
            </x-slot>

            {{-- Stats Cards --}}
            {{-- <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                @include('admin.articles.partials.stat-card', [
                    'icon' => 'comments',
                    'color' => 'blue',
                    'label' => 'Comentários',
                    'value' => $stats['total_comments'],
                ])
                @include('admin.articles.partials.stat-card', [
                    'icon' => 'check-circle',
                    'color' => 'green',
                    'label' => 'Aprovados',
                    'value' => $stats['approved_comments'],
                ])
                @include('admin.articles.partials.stat-card', [
                    'icon' => 'clock',
                    'color' => 'yellow',
                    'label' => 'Pendentes',
                    'value' => $stats['pending_comments'],
                ])
                @include('admin.articles.partials.stat-card', [
                    'icon' => 'ban',
                    'color' => 'red',
                    'label' => 'Spam',
                    'value' => $stats['spam_comments'],
                ])
                @include('admin.articles.partials.stat-card', [
                    'icon' => 'eye',
                    'color' => 'purple',
                    'label' => 'Visualizações',
                    'value' => number_format($stats['views']),
                ])
            </div> --}}

            {{-- Tabs de Navegação --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'comments'"
                        :class="{ 'border-green-600 text-green-600': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'comments' }"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center">
                        <i class="fas fa-comments mr-2"></i>
                        Comentários
                        @if ($stats['pending_comments'] > 0)
                            <span
                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-4">
                    <form method="GET" action="{{ route('admin.articles.show', $article) }}"
                        class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1 relative">
                            <i
                                class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="comment_search" value="{{ request('comment_search') }}"
                                placeholder="Buscar por nome, email ou conteúdo..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                        </div>
                        <select name="comment_status"
                            class="w-full sm:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                            <option value="all" {{ request('comment_status') == 'all' ? 'selected' : '' }}>Todos os
                                status</option>
                            <option value="approved" {{ request('comment_status') == 'approved' ? 'selected' : '' }}>
                                Aprovados</option>
                            <option value="pending" {{ request('comment_status') == 'pending' ? 'selected' : '' }}>
                                Pendentes</option>
                            <option value="spam" {{ request('comment_status') == 'spam' ? 'selected' : '' }}>Spam
                            </option>
                        </select>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-filter mr-2"></i>
                            Filtrar
                        </button>
                        @if (request()->hasAny(['comment_search', 'comment_status']) && request('comment_status') !== 'all')
                            <a href="{{ route('admin.articles.show', $article) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Limpar
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Bulk Actions Bar --}}
                <div x-show="selectedComments.length > 0" x-transition:enter="transition ease-out duration-200"
                    class="bg-green-50 rounded-xl border border-green-200 mb-6 p-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span x-text="selectedComments.length"></span> comentário(s) selecionado(s)
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="bulkActionType = 'approve'; confirmingBulkAction = true"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-green-300 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 transition-colors">
                                <i class="fas fa-check mr-1"></i>
                                Aprovar
                            </button>
                            <button type="button" @click="bulkActionType = 'spam'; confirmingBulkAction = true"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-yellow-300 rounded-lg text-sm font-medium text-yellow-700 hover:bg-yellow-50 transition-colors">
                                <i class="fas fa-ban mr-1"></i>
                                Marcar como Spam
                            </button>
                            <button type="button" @click="bulkActionType = 'delete'; confirmingBulkAction = true"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-red-300 rounded-lg text-sm font-medium text-red-700 hover:bg-red-50 transition-colors">
                                <i class="fas fa-trash mr-1"></i>
                                Mover para Lixeira
                            </button>
                            <button type="button" @click="selectedComments = []"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-1"></i>
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Lista de Comentários --}}
                <div class="space-y-4">
                    @forelse($article->comments as $comment)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            {{-- Cabeçalho do comentário --}}
                            <div
                                class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="h-8 w-8 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
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
                                    {{-- @include('admin.articles.partials.comment-status-badge', [
                                        'status' => $comment->status,
                                    ]) --}}
                                    <span
                                        class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            {{-- Conteúdo do comentário --}}
                            <div class="px-6 py-4">
                                <template x-if="editingCommentId === {{ $comment->id }}">
                                    <div>
                                        <textarea x-model="editingContent" rows="3"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"></textarea>
                                        <div class="mt-3 flex justify-end space-x-2">
                                            {{-- action="{{ route('admin.articles.comments.update', ['article' => $article, 'comment' => $comment]) }}" --}}
                                            <form method="POST"
                                                class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="content" x-model="editingContent">
                                                <button type="submit"
                                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fas fa-save mr-2"></i>
                                                    Salvar
                                                </button>
                                            </form>
                                            <button @click="editingCommentId = null"
                                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="editingCommentId !== {{ $comment->id }}">
                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $comment->content }}</p>
                                </template>

                                {{-- Ações do comentário --}}
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" x-model="selectedComments"
                                            value="{{ $comment->id }}"
                                            class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-200">
                                        <span class="text-xs text-gray-500">Selecionar</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if ($comment->status !== 'approved')
                                            <form method="POST"
                                                action="{{ route('admin.articles.comments.approve', ['article' => $article, 'comment' => $comment]) }}"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 text-gray-500 hover:text-green-600 rounded-lg hover:bg-green-50 transition-colors"
                                                    title="Aprovar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($comment->status !== 'spam')
                                            <form method="POST"
                                                action="{{ route('admin.articles.comments.spam', ['article' => $article, 'comment' => $comment]) }}"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 text-gray-500 hover:text-yellow-600 rounded-lg hover:bg-yellow-50 transition-colors"
                                                    title="Marcar como Spam">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- <button
                                            @click="editingCommentId = {{ $comment->id }}; editingContent = '{{ addslashes($comment->content) }}'"
                                            class="p-2 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                            title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button> --}}

                                        <form method="POST"
                                            action="{{ route('admin.articles.comments.destroy', ['article' => $article, 'comment' => $comment]) }}"
                                            class="inline"
                                            onsubmit="return confirm('Tem certeza que deseja mover este comentário para a lixeira?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-gray-500 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                title="Mover para lixeira">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Respostas --}}
                                @if ($comment->replies->count() > 0)
                                    <div class="mt-4 ml-8 space-y-4">
                                        @foreach ($comment->replies as $reply)
                                            {{-- @include('admin.articles.partials.comment-reply', [
                                                'reply' => $reply,
                                            ]) --}}
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Formulário de resposta --}}
                                <div x-show="replyingToId === {{ $comment->id }}" x-cloak class="mt-4">
                                    {{-- action="{{ route('admin.articles.comments.reply', ['article' => $article, 'comment' => $comment]) }}" --}}
                                    <form method="POST">
                                        @csrf
                                        <textarea name="content" x-model="replyContent" rows="2"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
                                            placeholder="Escreva sua resposta..."></textarea>
                                        <div class="mt-2 flex justify-end space-x-2">
                                            <button type="submit"
                                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                <i class="fas fa-reply mr-2"></i>
                                                Responder
                                            </button>
                                            <button type="button" @click="replyingToId = null"
                                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="mt-2">
                                    <button @click="replyingToId = {{ $comment->id }}; replyContent = ''"
                                        class="text-xs text-green-600 hover:text-green-700">
                                        <i class="fas fa-reply mr-1"></i>
                                        Responder
                                    </button>
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
                    @if (method_exists($article->comments, 'links'))
                        <div class="mt-4">
                            {{ $article->comments->links() }}
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
                                    {{ $article->type }}
                                    {{-- @include('admin.articles.partials.type-badge', [
                                        'type' => $article->type,
                                    ]) --}}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Edição</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $article->edition ?: '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Autor</dt>
                                <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                    <div
                                        class="h-6 w-6 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center mr-2">
                                        <span class="text-white text-xs font-medium">
                                            {{ strtoupper(substr($article->author?->name ?? 'S', 0, 1)) }}
                                        </span>
                                    </div>
                                    {{ $article->author?->name ?? 'Sistema' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tempo de leitura
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $article->reading_time_in_minutes }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $article->created_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Última
                                    atualização</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $article->updated_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>

                            @if ($article->published_at)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Publicado em
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $article->published_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Categorias --}}
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-folder text-green-600 mr-2"></i>
                            Categorias
                        </h3>

                        <div>
                            @forelse($article->categories as $category)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-2">
                                    <i class="fas fa-folder mr-1"></i>
                                    {{ $category->name }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-500">Sem categorias</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Excerto --}}
                    @if ($article->excerpt)
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-quote-right text-green-600 mr-2"></i>
                                Excerto
                            </h3>
                            <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $article->excerpt }}</p>
                        </div>
                    @endif

                    {{-- Conteúdo --}}
                    @if (in_array($article->type->value, ['article', 'news']) && $article->content)
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
                    @if ($article->type->value === 'video' && $article->youtube_url)
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-video text-green-600 mr-2"></i>
                                Vídeo
                            </h3>
                            <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden">
                                <iframe src="https://www.youtube.com/embed/{{ $article->youtube_id }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen class="w-full h-full"></iframe>
                            </div>
                        </div>
                    @endif

                    {{-- Imagem de destaque --}}
                    @if ($article->featured_image)
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-image text-green-600 mr-2"></i>
                                Imagem de destaque
                            </h3>
                            <img src="{{ $article->featured_image }}" alt="{{ $article->title }}"
                                class="max-w-full h-auto rounded-lg shadow-sm max-h-96">
                        </div>
                    @endif

                    {{-- SEO --}}
                    @if ($article->seo_title || $article->seo_description || $article->seo_keywords)
                        <div class="p-6">
                            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-chart-line text-green-600 mr-2"></i>
                                SEO
                            </h3>

                            <dl class="space-y-4">
                                @if ($article->seo_title)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Título
                                            SEO</dt>
                                        <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">
                                            {{ $article->seo_title }}</dd>
                                    </div>
                                @endif

                                @if ($article->seo_description)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Descrição SEO</dt>
                                        <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">
                                            {{ $article->seo_description }}</dd>
                                    </div>
                                @endif

                                @if ($article->seo_keywords)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Palavras-chave</dt>
                                        <dd class="mt-1">
                                            @foreach (explode(',', $article->seo_keywords) as $keyword)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800 mr-2 mb-2">
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
        {{-- @include('admin.articles.partials.bulk-action-modal') --}}

        {{-- Toast Notification --}}
        {{-- @include('admin.partials.toast') --}}
    </div>

</x-admin>
