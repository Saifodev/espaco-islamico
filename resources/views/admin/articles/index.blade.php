{{-- resources/views/admin/articles/index.blade.php --}}
<x-admin>
    <div class="min-h-screen bg-gray-50 py-8" x-data="{
        showFilters: {{ json_encode(request()->hasAny(['search', 'status', 'category_id', 'author_id'])) }},
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        selectedArticles: [],
        selectAll: false,
        confirmingAction: false,
        actionType: '',
        actionUrl: ''
    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <x-slot name="header">
                <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-serif font-semibold text-gray-800">Conteúdo</h1>
                        <p class="text-sm text-gray-600 mt-1">Gerencie todos os artigos, vídeos e publicações</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('admin.articles.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Novo Conteúdo
                        </a>
                    </div>
                </div>
            </x-slot>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-layer-group text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Publicados</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['published']) }}
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Rascunhos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['draft']) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gray-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-pen-alt text-2xl text-gray-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Arquivados</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['archived']) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-archive text-2xl text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search and Filters --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-6">
                    {{-- Search Bar --}}
                    <form method="GET" action="{{ route('admin.articles.index') }}"
                        class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1 relative">
                            <i
                                class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Buscar por título, conteúdo..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                        </div>

                        <button type="button" @click="showFilters = !showFilters"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-filter mr-2 text-gray-500"></i>
                            Filtros
                            @if (request()->hasAny(['status', 'category_id', 'author_id']))
                                <span
                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Ativos
                                </span>
                            @endif
                        </button>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Buscar
                        </button>

                        @if (request()->hasAny(['search', 'status', 'category_id', 'author_id']))
                            <a href="{{ route('admin.articles.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Limpar
                            </a>
                        @endif
                    </form>

                    {{-- Advanced Filters --}}
                    <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">

                        {{-- Status Filter --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" form="filter-form"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                <option value="">Todos</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Publicado
                                </option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Rascunho
                                </option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>
                                    Agendado
                                </option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>
                                    Arquivado
                                </option>
                            </select>
                        </div>

                        {{-- Category Filter --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                            <select name="category_id" form="filter-form"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                <option value="">Todas</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Author Filter --}}
                        @if ($users)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Autor</label>
                                <select name="author_id" form="filter-form"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                    <option value="">Todos</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('author_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    {{-- Form hidden para filtros --}}
                    <form id="filter-form" method="GET" action="{{ route('admin.articles.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                        <input type="hidden" name="author_id" value="{{ request('author_id') }}">
                    </form>
                </div>
            </div>

            {{-- Bulk Actions Bar --}}
            <div x-show="selectedArticles.length > 0" x-transition:enter="transition ease-out duration-200"
                class="bg-green-50 rounded-xl border border-green-200 mb-6 p-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span x-text="selectedArticles.length"></span> artigo(s) selecionado(s)
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button"
                            @click="actionType = 'publish'; actionUrl = '{{ route('admin.articles.comments.bulk', ['article' => 0]) }}'.replace('0', 'bulk'); confirmingAction = true"
                            class="inline-flex items-center px-3 py-1.5 bg-white border border-green-300 rounded-lg text-sm font-medium text-green-700 hover:bg-green-50 transition-colors">
                            <i class="fas fa-check mr-1"></i>
                            Publicar
                        </button>
                        <button type="button"
                            @click="actionType = 'archive'; actionUrl = '{{ route('admin.articles.comments.bulk', ['article' => 0]) }}'.replace('0', 'bulk'); confirmingAction = true"
                            class="inline-flex items-center px-3 py-1.5 bg-white border border-yellow-300 rounded-lg text-sm font-medium text-yellow-700 hover:bg-yellow-50 transition-colors">
                            <i class="fas fa-archive mr-1"></i>
                            Arquivar
                        </button>
                        <button type="button"
                            @click="actionType = 'delete'; actionUrl = '{{ route('admin.articles.comments.bulk', ['article' => 0]) }}'.replace('0', 'bulk'); confirmingAction = true"
                            class="inline-flex items-center px-3 py-1.5 bg-white border border-red-300 rounded-lg text-sm font-medium text-red-700 hover:bg-red-50 transition-colors">
                            <i class="fas fa-trash mr-1"></i>
                            Mover para lixeira
                        </button>
                        <button type="button" @click="selectedArticles = []"
                            class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-1"></i>
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Articles Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                {{-- <th scope="col" class="px-6 py-4 w-10">
                                    <input type="checkbox" x-model="selectAll"
                                        @change="selectedArticles = selectAll ? {{ $articles->pluck('id') }} : []"
                                        class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-200">
                                </th> --}}
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.articles.index', array_merge(request()->query(), ['sort_field' => 'title', 'sort_direction' => request('sort_direction') == 'asc' && request('sort_field') == 'title' ? 'desc' : 'asc'])) }}"
                                        class="group inline-flex items-center hover:text-gray-700">
                                        Título
                                        @if (request('sort_field') === 'title')
                                            <i
                                                class="fas fa-chevron-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }} ml-2 text-green-600 text-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col"
                                    class="hidden lg:table-cell px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th scope="col"
                                    class="hidden md:table-cell px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Categoria
                                </th>
                                <th scope="col"
                                    class="hidden xl:table-cell px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Autor
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.articles.index', array_merge(request()->query(), ['sort_field' => 'status', 'sort_direction' => request('sort_direction') == 'asc' && request('sort_field') == 'status' ? 'desc' : 'asc'])) }}"
                                        class="group inline-flex items-center hover:text-gray-700">
                                        Status
                                    </a>
                                </th>
                                <th scope="col"
                                    class="hidden sm:table-cell px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.articles.index', array_merge(request()->query(), ['sort_field' => 'created_at', 'sort_direction' => request('sort_direction') == 'asc' && request('sort_field') == 'created_at' ? 'desc' : 'asc'])) }}"
                                        class="group inline-flex items-center hover:text-gray-700">
                                        Data
                                        @if (request('sort_field') === 'created_at')
                                            <i
                                                class="fas fa-chevron-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }} ml-2 text-green-600 text-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($articles as $article)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- <td class="px-6 py-4">
                                        <input type="checkbox" x-model="selectedArticles"
                                            value="{{ $article->id }}"
                                            class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-200">
                                    </td> --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if ($article->featured_image)
                                                <img src="{{ $article->featured_image }}" alt=""
                                                    class="h-10 w-10 rounded-lg object-cover mr-3 hidden sm:block">
                                            @else
                                                <div
                                                    class="h-10 w-10 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mr-3 hidden sm:flex">
                                                    <i
                                                        class="fas {{ $article->type->value === 'video' ? 'fa-video' : ($article->type->value === 'newspaper' ? 'fa-newspaper' : 'fa-file-alt') }} text-gray-500"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('admin.articles.show', $article) }}"
                                                    class="text-sm font-medium text-gray-900 hover:text-green-600 transition-colors">
                                                    {{ $article->title }}
                                                </a>
                                                <div
                                                    class="text-xs text-gray-500 mt-1 flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center lg:hidden">
                                                        {{ $article->type_label }}
                                                    </span>
                                                    <span
                                                        class="hidden md:inline">{{ $article->categories->pluck('name')->implode(', ') ?: 'Sem categoria' }}</span>
                                                    <span class="text-green-600"><i
                                                            class="far fa-clock mr-1"></i>{{ $article->reading_time_in_minutes }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden lg:table-cell px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                    @if ($article->type->value === 'video') bg-purple-100 text-purple-800
                                    @elseif($article->type->value === 'newspaper') bg-blue-100 text-blue-800
                                    @elseif($article->type->value === 'news') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                            <i
                                                class="fas 
                                        @if ($article->type->value === 'video') fa-video mr-1
                                        @elseif($article->type->value === 'newspaper') fa-newspaper mr-1
                                        @elseif($article->type->value === 'news') fa-bolt mr-1
                                        @else fa-file-alt mr-1 @endif">
                                            </i>
                                            {{ $article->type_label }}
                                        </span>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 text-sm text-gray-600">
                                        {{ $article->categories->pluck('name')->implode(', ') ?: '-' }}
                                    </td>
                                    <td class="hidden xl:table-cell px-6 py-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <div
                                                class="h-6 w-6 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center mr-2">
                                                <span class="text-white text-xs font-medium">
                                                    {{ strtoupper(substr($article->author?->name ?? 'S', 0, 1)) }}
                                                </span>
                                            </div>
                                            {{ $article->author?->name ?? 'Sistema' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium 
                                    @if ($article->status->value === 'published') bg-green-100 text-green-800
                                    @elseif($article->status->value === 'draft') bg-gray-100 text-gray-800
                                    @elseif($article->status->value === 'scheduled') bg-yellow-100 text-yellow-800
                                    @elseif($article->status->value === 'archived') bg-red-100 text-red-800 @endif">
                                            <i
                                                class="fas 
                                        @if ($article->status->value === 'published') fa-check-circle mr-1
                                        @elseif($article->status->value === 'draft') fa-pen mr-1
                                        @elseif($article->status->value === 'scheduled') fa-clock mr-1
                                        @elseif($article->status->value === 'archived') fa-archive mr-1 @endif">
                                            </i>
                                            {{ $article->status_label }}
                                        </span>
                                        @if ($article->status->value === 'scheduled' && $article->published_at)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $article->published_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-4 text-sm text-gray-500">
                                        <div>{{ $article->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs">{{ $article->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.articles.edit', $article) }}"
                                                class="p-2 text-gray-500 hover:text-green-600 rounded-lg hover:bg-green-50 transition-colors"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.articles.show', $article) }}"
                                                class="p-2 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                                title="Ver detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if ($article->status->value === 'draft')
                                                <form action="{{ route('admin.articles.publish', $article) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 text-gray-500 hover:text-green-600 rounded-lg hover:bg-green-50 transition-colors"
                                                        title="Publicar">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($article->status->value !== 'archived')
                                                <form action="{{ route('admin.articles.archive', $article) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 text-gray-500 hover:text-yellow-600 rounded-lg hover:bg-yellow-50 transition-colors"
                                                        title="Arquivar">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.articles.restore', $article) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                                        title="Restaurar">
                                                        <i class="fas fa-trash-restore"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.articles.destroy', $article) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Tem certeza que deseja mover este conteúdo para a lixeira?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-gray-500 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                    title="Mover para lixeira">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <i class="fas fa-newspaper text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 text-sm">Nenhum conteúdo encontrado</p>
                                        <a href="{{ route('admin.articles.create') }}"
                                            class="inline-flex items-center mt-3 text-sm text-green-600 hover:text-green-700">
                                            <i class="fas fa-plus mr-1"></i>
                                            Criar novo conteúdo
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($articles->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $articles->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Confirmation Modal --}}
        <div x-show="confirmingAction" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            @click.self="confirmingAction = false">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                                :class="{
                                    'bg-green-100': actionType === 'publish',
                                    'bg-yellow-100': actionType === 'archive',
                                    'bg-red-100': actionType === 'delete'
                                }">
                                <i :class="{
                                    'fas fa-check-circle text-green-600': actionType === 'publish',
                                    'fas fa-archive text-yellow-600': actionType === 'archive',
                                    'fas fa-exclamation-triangle text-red-600': actionType === 'delete'
                                }"
                                    class="text-xl"></i>
                            </div>

                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900"
                                    x-text="{
                                'publish': 'Publicar Conteúdo',
                                'archive': 'Arquivar Conteúdo',
                                'delete': 'Mover para Lixeira'
                            }[actionType]">
                                </h3>

                                <div class="mt-2">
                                    <p class="text-sm text-gray-500"
                                        x-text="{
                                    'publish': 'Tem certeza que deseja aplicar esta ação nos itens selecionados?',
                                    'archive': 'Tem certeza que deseja arquivar os itens selecionados?',
                                    'delete': 'Tem certeza que deseja mover os itens selecionados para a lixeira?'
                                }[actionType]">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
                        <form :action="actionUrl" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" :value="actionType">
                            <template x-for="id in selectedArticles" :key="id">
                                <input type="hidden" name="comments[]" :value="id">
                            </template>
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white sm:ml-3 sm:w-auto"
                                :class="{
                                    'bg-green-600 hover:bg-green-700': actionType === 'publish',
                                    'bg-yellow-600 hover:bg-yellow-700': actionType === 'archive',
                                    'bg-red-600 hover:bg-red-700': actionType === 'delete'
                                }">
                                <i
                                    :class="{
                                        'fas fa-check-circle mr-2': actionType === 'publish',
                                        'fas fa-archive mr-2': actionType === 'archive',
                                        'fas fa-trash mr-2': actionType === 'delete'
                                    }"></i>
                                Confirmar
                            </button>
                        </form>
                        <button type="button" @click="confirmingAction = false"
                            class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>