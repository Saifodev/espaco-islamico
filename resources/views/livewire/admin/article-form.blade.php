{{-- resources/views/livewire/admin/article-form.blade.php --}}
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                {{-- <h1 class="text-3xl font-serif font-semibold text-gray-800">
                    {{ $isEditing ? 'Editar' : 'Novo' }} {{ ucfirst($type) }}
                </h1> --}}
                <p class="text-sm text-gray-600 mt-1">
                    Preencha os campos abaixo para {{ $isEditing ? 'atualizar' : 'criar' }} o conteúdo
                </p>
            </div>

            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('admin.articles.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar
                </a>
                <button type="button" wire:click="save"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    {{ $isEditing ? 'Atualizar' : 'Salvar' }}
                </button>
            </div>
        </div>

        {{-- Main Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <form wire:submit.prevent="save" class="divide-y divide-gray-200">

                {{-- Basic Info Section --}}
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">

                        {{-- Tipo de Conteúdo --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Conteúdo <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="type" id="type" {{ $isEditing ? 'disabled' : '' }}
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors @if ($isEditing) bg-gray-100 cursor-not-allowed @endif">
                                @foreach ($contentTypes as $typeOption)
                                    <option value="{{ $typeOption['value'] }}">
                                        {{ $typeOption['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Título --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Título <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="title" id="title"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
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
                                <input type="text" wire:model="slug" id="slug"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg lg:rounded-l-none lg:border-l-0 focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
                                    placeholder="url-do-artigo">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Deixe em branco para gerar automaticamente</p>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campos Específicos por Tipo --}}
                        @if ($type === 'video')
                            <div>
                                <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    URL do YouTube <span class="text-red-500">*</span>
                                </label>
                                <input type="url" wire:model="youtube_url" id="youtube_url"
                                    placeholder="https://youtube.com/watch?v=..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                @error('youtube_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if ($type === 'newspaper')
                            <div>
                                <label for="edition" class="block text-sm font-medium text-gray-700 mb-2">
                                    Número da Edição <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="edition" id="edition"
                                    placeholder="Ex: Edição 25 - Março 2026"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                @error('edition')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Resumo --}}
                        <div>
                            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $type === 'article' ? 'Resumo' : 'Descrição' }}
                            </label>
                            <textarea wire:model="excerpt" id="excerpt" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
                                placeholder="Breve descrição do conteúdo..."></textarea>
                            @error('excerpt')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Conteúdo --}}
                        @if (in_array($type, ['article', 'news']))
                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                    Conteúdo <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="content" id="content" rows="15"
                                    class="tinymce w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors font-mono"></textarea>
                                @error('content')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Featured Image Section --}}
                @if ($type !== 'newspaper')
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
                                <div
                                    class="border-2 border-dashed border-gray-300 rounded-lg p-4 {{ $featured_image_url ? 'bg-gray-50' : '' }}">
                                    @if ($featured_image_url)
                                        <img src="{{ $featured_image_url }}" alt="Preview"
                                            class="max-w-full h-auto rounded-lg shadow-sm mx-auto max-h-60">
                                    @else
                                        <div class="text-center py-12">
                                            <i class="fas fa-image text-4xl text-gray-300 mb-3"></i>
                                            <p class="text-sm text-gray-500">Nenhuma imagem selecionada</p>
                                        </div>
                                    @endif
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
                                                {{ $featured_image_url ? 'Substituir imagem' : 'Escolher imagem' }}
                                            </span>
                                            <input type="file" wire:model="featured_image_temp" class="sr-only"
                                                accept="image/jpeg,image/png,image/webp">
                                        </label>

                                        @if ($featured_image_temp || $featured_image_url)
                                            <button type="button" wire:click="removeFeaturedImage"
                                                class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                                <i class="fas fa-trash mr-2"></i>
                                                Remover
                                            </button>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        PNG, JPG ou WebP até 5MB • Recomendado 1200x630px
                                    </p>

                                    @if ($featured_image_temp)
                                        <div class="text-sm text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            {{ $featured_image_temp->getClientOriginalName() }}
                                        </div>
                                    @endif

                                    @error('featured_image_temp')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    <div wire:loading wire:target="featured_image_temp">
                                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                                            <i class="fas fa-spinner fa-spin"></i>
                                            <span>Enviando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- PDF Section para Newspaper --}}
                @if ($type === 'newspaper')
                    <div class="p-6">
                        <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                            Arquivo PDF da Edição
                        </h3>

                        <div class="space-y-4">
                            {{-- Preview do PDF atual --}}
                            @if ($pdf_url)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-pdf text-2xl text-red-500 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $pdf_name ?? 'Arquivo PDF' }}</p>
                                            <p class="text-xs text-gray-500">
                                                <a href="{{ $pdf_url }}" target="_blank"
                                                    class="text-green-600 hover:text-green-700">
                                                    <i class="fas fa-external-link-alt mr-1"></i>
                                                    Visualizar PDF
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removePdf"
                                        class="p-2 text-red-600 hover:text-red-700 rounded-lg hover:bg-red-50 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endif

                            {{-- Upload de novo PDF --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $pdf_url ? 'Substituir PDF' : 'Selecionar PDF' }}
                                </label>
                                <div class="flex items-center space-x-3">
                                    <label class="relative cursor-pointer">
                                        <span
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-upload mr-2 text-gray-500"></i>
                                            {{ $pdf_url ? 'Substituir PDF' : 'Escolher PDF' }}
                                        </span>
                                        <input type="file" wire:model="pdf_temp" class="sr-only"
                                            accept="application/pdf">
                                    </label>

                                    @if ($pdf_temp)
                                        <span class="text-sm text-gray-600">
                                            <i class="fas fa-check-circle text-green-600 mr-1"></i>
                                            {{ $pdf_temp->getClientOriginalName() }}
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-2 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    PDF até 20MB • Apenas arquivos PDF são permitidos
                                </p>

                                @error('pdf_temp')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <div wire:loading wire:target="pdf_temp">
                                    <div class="flex items-center space-x-2 text-sm text-gray-600 mt-2">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <span>Enviando PDF...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Categories & Status --}}
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if ($type !== 'news')
                            {{-- Categorias --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Categorias
                                </label>
                                <div class="space-y-2 max-h-60 overflow-y-auto p-3 border border-gray-200 rounded-lg">
                                    @foreach ($categories as $category)
                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model="selectedCategories"
                                                value="{{ $category->id }}" id="category_{{ $category->id }}"
                                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-200">
                                            <label for="category_{{ $category->id }}"
                                                class="ml-3 text-sm text-gray-700">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Selecione uma ou mais categorias</p>
                            </div>
                        @endif

                        {{-- Status e Publicação --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="status" id="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                @foreach ($statusOptions as $statusOption)
                                    <option value="{{ $statusOption['value'] }}">
                                        {{ $statusOption['label'] }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($status === 'scheduled')
                                <div class="mt-4">
                                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Data de Publicação
                                    </label>
                                    <input type="datetime-local" wire:model="published_at" id="published_at"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Hidden submit for enter key --}}
                <button type="submit" class="hidden">Submit</button>
            </form>
        </div>

        {{-- Success/Error Messages --}}
        @if (session()->has('success'))
            <div
                class="mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2 text-green-600"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Loading Indicator --}}
        <div wire:loading.delay class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-4 flex items-center space-x-3">
                <i class="fas fa-spinner fa-spin text-green-600"></i>
                <span class="text-sm text-gray-700">Salvando...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- <script src="https://cdn.tiny.cloud/1/{{ env('TINY_MCE_API_KEY') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> --}}
    <script>
        tinymce.init({
            selector: '.tinymce',
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic backcolor | image | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat ', //| help',
            content_style: 'body { font-family:Figtree,Helvetica,Arial,sans-serif; font-size:16px; line-height:1.6; color:#333; }',
            mobile: {
                menubar: true,
                toolbar: 'undo redo | bold italic | bullist numlist'
            }
        });
    </script>
@endpush
