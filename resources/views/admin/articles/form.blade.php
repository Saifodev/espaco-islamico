{{-- resources/views/admin/articles/form.blade.php --}}
<x-admin>
    <div class="min-h-screen bg-gray-50 py-8" x-data="articleForm()" x-init="init(@json($article ?? null), @json(old('_token')))">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <x-slot name="header">
                <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-serif font-semibold text-gray-800">
                            {{ isset($article) ? 'Editar' : 'Novo' }}
                            {{ ucfirst(old('type', $article->type->value ?? 'article')) }}
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Preencha os campos abaixo para {{ isset($article) ? 'atualizar' : 'criar' }} o conteúdo
                        </p>
                    </div>

                    <div class="mt-4 md:mt-0 flex items-center space-x-3">
                        <a href="{{ route('admin.articles.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Voltar
                        </a>
                        <button type="submit" form="article-form"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            {{ isset($article) ? 'Atualizar' : 'Salvar' }}
                        </button>
                    </div>
                </div>
            </x-slot>

            {{-- Main Form --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form id="article-form" method="POST"
                    action="{{ isset($article) ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
                    enctype="multipart/form-data" class="divide-y divide-gray-200">
                    @csrf
                    @if (isset($article))
                        @method('PUT')
                    @endif

                    {{-- Basic Info Section --}}
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6">

                            {{-- Tipo de Conteúdo --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Conteúdo <span class="text-red-500">*</span>
                                </label>
                                <select x-model="type" name="type" id="type"
                                    {{ isset($article) ? 'disabled' : '' }}
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @if (isset($article)) bg-gray-100 cursor-not-allowed @endif">
                                    @foreach ($contentTypes as $typeOption)
                                        <option value="{{ $typeOption['value'] }}"
                                            {{ old('type', $article->type->value ?? 'article') == $typeOption['value'] ? 'selected' : '' }}>
                                            {{ $typeOption['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (isset($article))
                                    <input type="hidden" name="type" value="{{ $article->type->value }}">
                                @endif
                                @error('type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Título --}}
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Título <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" x-model="title"
                                    @input.debounce.500ms="generateSlug"
                                    value="{{ old('title', $article->title ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @error('title') border-red-500 @enderror"
                                    placeholder="Digite o título">
                                @error('title')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Slug --}}
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                    Slug (URL)
                                </label>
                                <div class="flex rounded-lg">
                                    <span
                                        class="hidden lg:inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                        {{ config('app.url') }}conteudo/
                                    </span>
                                    <input type="text" name="slug" id="slug" x-model="slug"
                                        value="{{ old('slug', $article->slug ?? '') }}"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg lg:rounded-l-none lg:border-l-0 focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @error('slug') border-red-500 @enderror"
                                        placeholder="url-do-artigo">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Deixe em branco para gerar automaticamente</p>
                                @error('slug')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Campos Específicos por Tipo --}}
                            <div x-show="type === 'video'" x-cloak>
                                <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    URL do YouTube <span class="text-red-500">*</span>
                                </label>
                                <input type="url" name="youtube_url" id="youtube_url"
                                    value="{{ old('youtube_url', $article->youtube_url ?? '') }}"
                                    placeholder="https://youtube.com/watch?v=..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @error('youtube_url') border-red-500 @enderror">
                                @error('youtube_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-show="type === 'newspaper'" x-cloak>
                                <label for="edition" class="block text-sm font-medium text-gray-700 mb-2">
                                    Edição <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="edition" id="edition"
                                    value="{{ old('edition', $article->edition ?? '') }}"
                                    placeholder="Ex: Edição 25 - Março 2026"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @error('edition') border-red-500 @enderror">
                                @error('edition')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Vendivel --}}
                            <div x-show="type === 'newspaper'" x-cloak>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Disponível para Venda?
                                </label>
                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center">
                                        <input type="radio" name="is_sellable" id="sellable_yes" value="1"
                                            {{ old('is_sellable', $article->is_sellable ?? false) ? 'checked' : '' }}
                                            class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                        <label for="sellable_yes" class="ml-2 text-sm text-gray-700">
                                            Sim (Vendível)
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="is_sellable" id="sellable_no" value="0"
                                            {{ !old('is_sellable', $article->is_sellable ?? false) ? 'checked' : '' }}
                                            class="h-4 w-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                        <label for="sellable_no" class="ml-2 text-sm text-gray-700">
                                            Não (Grátis)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Preço e WhatsApp --}}
                            <div x-show="type === 'newspaper' && is_sellable" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                        Preço (MT) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="price" id="price" step="0.01" min="0"
                                        value="{{ old('price', $article->price ?? '') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @error('price') border-red-500 @enderror"
                                        placeholder="Ex: 49.90">
                                    @error('price')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Número do WhatsApp <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="whatsapp_number" id="whatsapp_number"
                                        value="{{ old('whatsapp_number', $article->whatsapp_number ?? '') }}"
                                        placeholder="Ex: 25884312345"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @error('whatsapp_number') border-red-500 @enderror">
                                    @error('whatsapp_number')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Resumo --}}
                            <div>
                                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ old('type', $article->type->value ?? 'article') === 'article' ? 'Resumo' : 'Descrição' }}
                                </label>
                                <textarea name="excerpt" id="excerpt" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @error('excerpt') border-red-500 @enderror"
                                    placeholder="Breve descrição do conteúdo...">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                                @error('excerpt')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Conteúdo --}}
                            <div x-show="type === 'article' || type === 'news'" x-cloak>
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                    Conteúdo <span class="text-red-500">*</span>
                                </label>
                                <textarea name="content" id="content" rows="15"
                                    class="tinymce w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @error('content') border-red-500 @enderror">{{ old('content', $article->content ?? '') }}</textarea>
                                @error('content')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Featured Image Section --}}
                    @if (!isset($article) || $article->type->value !== 'newspaper')
                        <div class="p-6">
                            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-image text-green-600 mr-2"></i>
                                Imagem de Destaque
                            </h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- Preview da Imagem --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Preview
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                                        <template x-if="preview">
                                            <img :src="preview" alt="Preview"
                                                class="max-w-full h-auto rounded-lg shadow-sm mx-auto max-h-60">
                                        </template>
                                        <template
                                            x-if="!preview && '{{ isset($featuredImage) ? $featuredImage->getUrl() : '' }}'">
                                            <img src="{{ isset($featuredImage) ? $featuredImage->getUrl() : '' }}"
                                                alt="Preview atual"
                                                class="max-w-full h-auto rounded-lg shadow-sm mx-auto max-h-60">
                                        </template>
                                        <template x-if="!preview && !'{{ isset($featuredImage) ? 'true' : '' }}'">
                                            <div class="text-center py-12">
                                                <i class="fas fa-image text-4xl text-gray-300 mb-3"></i>
                                                <p class="text-sm text-gray-500">Nenhuma imagem selecionada</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Upload --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Selecionar Imagem
                                    </label>
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <label class="relative cursor-pointer">
                                                <span
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                    <i class="fas fa-upload mr-2 text-gray-500"></i>
                                                    {{ isset($featuredImage) ? 'Substituir imagem' : 'Escolher imagem' }}
                                                </span>
                                                <input type="file" name="featured_image" class="sr-only"
                                                    accept="image/jpeg,image/png,image/webp"
                                                    @change="previewImage($event)">
                                            </label>

                                            @if (isset($featuredImage))
                                                <div class="ml-3 flex items-center">
                                                    <input type="checkbox" name="remove_featured_image"
                                                        id="remove_featured_image" value="1"
                                                        class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-200">
                                                    <label for="remove_featured_image"
                                                        class="ml-2 text-sm text-red-600">
                                                        Remover imagem atual
                                                    </label>
                                                </div>
                                            @endif
                                        </div>

                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            PNG, JPG ou WebP até 5MB • Recomendado 1200x630px
                                        </p>

                                        @error('featured_image')
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- PDF Section para Newspaper --}}
                    <div x-show="type === 'newspaper'" x-cloak class="p-6">
                        <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                            Arquivo PDF da Edição
                        </h3>

                        <div class="space-y-4">
                            {{-- Preview do PDF atual --}}
                            @if (isset($pdfFile))
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-pdf text-2xl text-red-500 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $pdfFile->file_name }}</p>
                                            <p class="text-xs text-gray-500">
                                                <a href="{{ $pdfFile->getUrl() }}" target="_blank"
                                                    class="text-green-600 hover:text-green-700">
                                                    <i class="fas fa-external-link-alt mr-1"></i>
                                                    Visualizar PDF
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="remove_pdf" id="remove_pdf" value="1"
                                            class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-200">
                                        <label for="remove_pdf" class="ml-2 text-sm text-red-600">
                                            Remover
                                        </label>
                                    </div>
                                </div>
                            @endif

                            {{-- Upload de novo PDF --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ isset($pdfFile) ? 'Substituir PDF' : 'Selecionar PDF' }}
                                </label>
                                <div class="p-4 flex items-center justify-start gap-4">
                                    <div class="flex items-center space-x-3">
                                        <label class="relative cursor-pointer">
                                            <span
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                <i class="fas fa-upload mr-2 text-gray-500"></i>
                                                {{ isset($pdfFile) ? 'Substituir PDF' : 'Escolher PDF' }}
                                            </span>
                                            <input type="file" name="pdf_file" class="sr-only"
                                                accept="application/pdf" @change="previewPdf($event)">
                                        </label>
                                    </div>

                                    <template x-if="pdfName">
                                        <div class="flex items-center mt-3 text-sm text-gray-700">
                                            <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                            <span x-text="pdfName"></span>
                                        </div>
                                    </template>
                                </div>

                                <p class="mt-2 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    PDF até 20MB • Apenas arquivos PDF são permitidos
                                </p>

                                @error('pdf_file')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Categories & Status --}}
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Categorias --}}
                            <div x-show="type !== 'news'" x-cloak>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Categorias
                                </label>
                                <div class="space-y-2 max-h-60 overflow-y-auto p-3 border border-gray-200 rounded-lg">
                                    @foreach ($categories as $category)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                id="category_{{ $category->id }}"
                                                {{ in_array($category->id, old('categories', $article?->categories?->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-200">
                                            <label for="category_{{ $category->id }}"
                                                class="ml-3 text-sm text-gray-700">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Selecione uma ou mais categorias</p>
                                @error('categories')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status e Publicação --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" x-model="status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                    @foreach ($statusOptions as $statusOption)
                                        <option value="{{ $statusOption['value'] }}"
                                            {{ old('status', $article->status->value ?? 'draft') == $statusOption['value'] ? 'selected' : '' }}>
                                            {{ $statusOption['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <div x-show="status === 'scheduled'" x-cloak class="mt-4">
                                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Data de Publicação
                                    </label>
                                    <input type="datetime-local" name="published_at" id="published_at"
                                        value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                    @error('published_at')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SEO Section --}}
                    <div class="p-6">
                        <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-chart-line text-green-600 mr-2"></i>
                            SEO (Opcional)
                        </h3>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="seo_title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Título SEO
                                </label>
                                <input type="text" name="seo_title" id="seo_title"
                                    value="{{ old('seo_title', $article->seo_title ?? '') }}" maxlength="70"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
                                    placeholder="Título para mecanismos de busca (máx. 70 caracteres)">
                                <p class="mt-1 text-xs text-gray-500"
                                    x-text="$refs.seo_title?.value.length + '/70 caracteres'"></p>
                            </div>

                            <div>
                                <label for="seo_description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Descrição SEO
                                </label>
                                <textarea name="seo_description" id="seo_description" rows="2" maxlength="160"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
                                    placeholder="Descrição para mecanismos de busca (máx. 160 caracteres)">{{ old('seo_description', $article->seo_description ?? '') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500"
                                    x-text="$refs.seo_description?.value.length + '/160 caracteres'"></p>
                            </div>

                            <div>
                                <label for="seo_keywords" class="block text-sm font-medium text-gray-700 mb-2">
                                    Palavras-chave
                                </label>
                                <input type="text" name="seo_keywords" id="seo_keywords"
                                    value="{{ old('seo_keywords', $article->seo_keywords ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
                                    placeholder="palavra-chave1, palavra-chave2, palavra-chave3">
                                <p class="mt-1 text-xs text-gray-500">Separe as palavras-chave por vírgula</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function articleForm() {
                return {
                    preview: null,
                    pdfName: null,
                    type: '{{ old('type', $article->type->value ?? 'article') }}',
                    title: '{{ old('title', $article->title ?? '') }}',
                    slug: '{{ old('slug', $article->slug ?? '') }}',
                    status: '{{ old('status', $article->status->value ?? 'draft') }}',

                    generateSlug() {
                        if (!this.slug || this.slug === this.title.toLowerCase().replace(/[^a-z0-9]+/g, '-')) {
                            this.slug = this.title.toLowerCase()
                                .replace(/[áàãâä]/g, 'a')
                                .replace(/[éèêë]/g, 'e')
                                .replace(/[íìîï]/g, 'i')
                                .replace(/[óòõôö]/g, 'o')
                                .replace(/[úùûü]/g, 'u')
                                .replace(/[ç]/g, 'c')
                                .replace(/[^a-z0-9]+/g, '-')
                                .replace(/^-+|-+$/g, '');

                            document.getElementById('slug').value = this.slug;
                        }
                    },

                    previewImage(event) {
                        const file = event.target.files[0];

                        if (!file) return;

                        const reader = new FileReader();

                        reader.onload = (e) => {
                            this.preview = e.target.result;
                        };

                        reader.readAsDataURL(file);
                    },

                    previewPdf(event) {
                        const file = event.target.files[0];
                        if (!file) return;

                        this.pdfName = file.name;
                    },

                    init(article, token) {
                        // Inicializar TinyMCE
                        tinymce.init({
                            selector: '.tinymce',
                            license_key: 'gpl',
                            height: 500,
                            menubar: false,
                            plugins: [
                                'advlist', 'autolink', 'lists', 'link', 'image', 'editimage', 'tinydrive',
                                'charmap', 'preview',
                                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                'insertdatetime', 'media', 'table', 'help', 'wordcount',
                            ],
                            toolbar: 'undo redo | blocks | bold italic backcolor | image | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | fullscreen',
                            content_style: 'body { font-family:Figtree,Helvetica,Arial,sans-serif; font-size:16px; line-height:1.6; color:#333; }',
                            mobile: {
                                menubar: true,
                                toolbar: 'undo redo | bold italic | bullist numlist'
                            },
                            setup: function(editor) {
                                editor.on('change', function() {
                                    tinymce.triggerSave();
                                });
                            }
                        });
                    }
                }
            }
        </script>
    @endpush
</x-admin>
