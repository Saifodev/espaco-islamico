{{-- resources/views/livewire/admin/article-form.blade.php --}}
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-full px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $isEditing ? 'Editar' : 'Novo' }} {{ ucfirst($type) }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Preencha os campos abaixo para {{ $isEditing ? 'atualizar' : 'criar' }} o conteúdo
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex space-x-3">
                <a href="{{ route('admin.articles.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Cancelar
                </a>
                <button type="button" wire:click="save"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                        </path>
                    </svg>
                    {{ $isEditing ? 'Atualizar' : 'Salvar' }}
                </button>
            </div>
        </div>

        {{-- Main Form --}}
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <form wire:submit.prevent="save" class="divide-y divide-gray-200">

                {{-- Basic Info Section --}}
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6">

                        {{-- Tipo de Conteúdo (Dropdown Simples) --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Tipo de Conteúdo <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="type" id="type" {{ $isEditing ? 'disabled' : '' }}
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md @if ($isEditing) bg-gray-100 cursor-not-allowed @endif">
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
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Título <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="title" id="title"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">
                                Slug (URL)
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <span
                                    class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    {{ config('app.url') }}/blog/
                                </span>
                                <input type="text" wire:model="slug" id="slug"
                                    class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Deixe em branco para gerar automaticamente</p>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campos Específicos por Tipo --}}
                        @if ($type === 'video')
                            <div>
                                <label for="youtube_url" class="block text-sm font-medium text-gray-700">
                                    URL do YouTube <span class="text-red-500">*</span>
                                </label>
                                <input type="url" wire:model="youtube_url" id="youtube_url"
                                    placeholder="https://youtube.com/watch?v=..."
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                @error('youtube_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if ($type === 'newspaper')
                            <div>
                                <label for="edition" class="block text-sm font-medium text-gray-700">
                                    Número da Edição <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="edition" id="edition"
                                    placeholder="Ex: Edição 25 - Março 2026"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                @error('edition')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Campo de Upload de PDF para Jornal --}}
                            <div class="mt-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Arquivo PDF da Edição</h4>

                                <div class="space-y-4">
                                    {{-- Preview do PDF atual --}}
                                    @if ($pdf_url)
                                        <div
                                            class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                                            <div class="flex items-center">
                                                <svg class="h-8 w-8 text-red-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z" />
                                                    <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                                                </svg>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $pdf_name ?? 'Arquivo PDF' }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        <a href="{{ $pdf_url }}" target="_blank"
                                                            class="text-green-600 hover:text-green-800">
                                                            Visualizar PDF
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                            <button type="button" wire:click="removePdf"
                                                class="inline-flex items-center p-1 border border-transparent rounded-full text-red-600 hover:bg-red-100">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif

                                    {{-- Upload de novo PDF --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $pdf_url ? 'Substituir PDF' : 'Selecionar PDF' }}
                                        </label>
                                        <div class="flex items-center space-x-3">
                                            <label
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none">
                                                <span
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                                        </path>
                                                    </svg>
                                                    {{ $pdf_url ? 'Substituir PDF' : 'Escolher PDF' }}
                                                </span>
                                                <input type="file" wire:model="pdf_temp" class="sr-only"
                                                    accept="application/pdf">
                                            </label>

                                            @if ($pdf_temp)
                                                <span class="text-sm text-gray-500">
                                                    {{ $pdf_temp->getClientOriginalName() }}
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-2 text-xs text-gray-500">
                                            PDF até 20MB • Apenas arquivos PDF são permitidos
                                        </p>

                                        @error('pdf_temp')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Barra de progresso do upload --}}
                                    <div wire:loading wire:target="pdf_temp" class="mt-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="flex-1">
                                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-2 bg-green-500 rounded-full animate-pulse"
                                                        style="width: 50%"></div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500">Enviando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Resumo --}}
                        <div>
                            <label for="excerpt" class="block text-sm font-medium text-gray-700">
                                {{ $type === 'article' ? 'Resumo' : 'Descrição' }}
                            </label>
                            <textarea wire:model="excerpt" id="excerpt" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                            @error('excerpt')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Conteúdo (para artigos) --}}
                        @if ($type === 'article')
                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700">
                                    Conteúdo <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="content" id="content" rows="15"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm font-mono"></textarea>
                                @error('content')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Featured Image Section --}}
                @if ($type !== 'newspaper')
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Imagem de Destaque</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Preview da Imagem --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Preview
                            </label>
                            <div
                                class="border-2 border-gray-300 border-dashed rounded-lg p-4 {{ $featured_image_url ? 'bg-gray-50' : '' }}">
                                @if ($featured_image_url)
                                    <img src="{{ $featured_image_url }}" alt="Preview"
                                        class="max-w-full h-auto rounded-lg shadow-sm mx-auto"
                                        style="max-height: 200px;">
                                @else
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Nenhuma imagem selecionada</p>
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
                                    <label
                                        class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none">
                                        <span
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                                </path>
                                            </svg>
                                            Escolher imagem
                                        </span>
                                        <input type="file" wire:model="featured_image_temp" class="sr-only"
                                            accept="image/jpeg,image/png,image/webp">
                                    </label>
                                    @if ($featured_image_temp || $featured_image_url)
                                        {{-- @if ($featured_image_temp || ($isEditing && $article->featured_image)) --}}
                                        <button type="button" wire:click="removeFeaturedImage"
                                            class="ml-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                            Remover
                                        </button>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">
                                    PNG, JPG ou WebP até 5MB • Recomendado 1200x630px
                                </p>
                                @error('featured_image_temp')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Categories & Status --}}
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Categorias --}}
                        <div>
                            <label for="categories" class="block text-sm font-medium text-gray-700 mb-2">
                                Categorias
                            </label>
                            <select wire:model="selectedCategories" id="categories" multiple size="5"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" class="py-1">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-gray-500">Segure Ctrl/Cmd para selecionar múltiplas</p>
                        </div>

                        {{-- Status e Publicação --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="status" id="status"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                                @foreach ($statusOptions as $statusOption)
                                    <option value="{{ $statusOption['value'] }}">
                                        {{ $statusOption['label'] }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($status === 'scheduled')
                                <div class="mt-4">
                                    <label for="published_at" class="block text-sm font-medium text-gray-700">
                                        Data de Publicação
                                    </label>
                                    <input type="datetime-local" wire:model="published_at" id="published_at"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
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
            <div class="mt-4 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Loading Indicator --}}
        <div wire:loading.delay class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-4 flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-sm text-gray-700">Salvando...</span>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Loading animation */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Select multiple styling */
        select[multiple] {
            padding: 0.5rem;
        }

        select[multiple] option {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        select[multiple] option:checked {
            background: #10b981 linear-gradient(0deg, #10b981 0%, #10b981 100%);
            color: white;
        }
    </style>
@endpush
