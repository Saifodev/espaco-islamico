{{-- resources/views/livewire/admin/article-form.blade.php --}}
@php
    use App\Domains\Content\Enums\ContentType;
@endphp
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Cabeçalho --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ $article ? 'Editar' : 'Novo' }} Conteúdo
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Preencha os campos abaixo. Campos marcados com <span class="text-red-500">*</span> são obrigatórios.
            </p>
        </div>

        <form wire:submit.prevent="save" class="space-y-8">
            
            {{-- Mensagens de erro de publicação --}}
            @error('publish')
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Não é possível publicar:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>{{ $message }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @enderror

            {{-- Seletor de Tipo de Conteúdo --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    Tipo de Conteúdo <span class="text-red-500">*</span>
                </label>
                
                @if($confirmingTypeChange)
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span class="text-sm text-yellow-800">
                                    Alterar o tipo pode remover campos específicos. Deseja continuar?
                                </span>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" 
                                        wire:click="confirmTypeChange"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                                    Sim, alterar
                                </button>
                                <button type="button" 
                                        wire:click="cancelTypeChange"
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($contentTypes as $typeOption)
                        <button type="button"
                                wire:click="changeType('{{ $typeOption['value'] }}')"
                                class="relative block p-6 border-2 rounded-lg transition-all duration-200
                                    {{ $type === $typeOption['value'] 
                                        ? 'border-' . $typeOption['color'] . '-500 bg-' . $typeOption['color'] . '-50' 
                                        : 'border-gray-200 hover:border-' . $typeOption['color'] . '-300' }}">
                            
                            {{-- Indicador selecionado --}}
                            @if($type === $typeOption['value'])
                                <div class="absolute top-2 right-2">
                                    <svg class="h-5 w-5 text-{{ $typeOption['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            {{-- Ícone --}}
                            <div class="flex justify-center mb-3">
                                <svg class="h-8 w-8 text-{{ $typeOption['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $typeOption['icon'] }}"></path>
                                </svg>
                            </div>
                            
                            {{-- Label --}}
                            <div class="text-center">
                                <span class="block text-sm font-medium text-gray-900">
                                    {{ $typeOption['label'] }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
                @error('type') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Campos Principais --}}
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                
                {{-- Título --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               wire:model="title" 
                               id="title"
                               placeholder="{{ $placeholders['title'] ?? 'Digite o título...' }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm">
                    </div>
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug (URL)</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                            {{ config('app.url') }}/blog/
                        </span>
                        <input type="text" 
                               wire:model="slug" 
                               id="slug"
                               class="flex-1 block w-full rounded-none rounded-r-lg border-gray-300 focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Deixe em branco para gerar automaticamente</p>
                    @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Campos Específicos por Tipo --}}
                
                {{-- URL do YouTube (para vídeos) --}}
                @if($visibleFields['youtube_url'])
                <div class="border-t border-gray-200 pt-4">
                    <label for="youtube_url" class="block text-sm font-medium text-gray-700">
                        URL do YouTube 
                        @if($contentType->isRequiredForPublishing('youtube_url'))
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <div class="mt-1">
                        <input type="url" 
                               wire:model="youtube_url" 
                               id="youtube_url"
                               placeholder="{{ $placeholders['youtube_url'] ?? 'https://youtube.com/watch?v=...' }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Cole a URL completa do vídeo do YouTube
                    </p>
                    @error('youtube_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- Edição (para jornais) --}}
                @if($visibleFields['edition'])
                <div class="border-t border-gray-200 pt-4">
                    <label for="edition" class="block text-sm font-medium text-gray-700">
                        Número da Edição 
                        @if($contentType->isRequiredForPublishing('edition'))
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               wire:model="edition" 
                               id="edition"
                               placeholder="{{ $placeholders['edition'] ?? 'Ex: Edição 25 - Março 2026' }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm">
                    </div>
                    @error('edition') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- Resumo/Descrição --}}
                @if($visibleFields['content'])
                <div class="border-t border-gray-200 pt-4">
                    <label for="excerpt" class="block text-sm font-medium text-gray-700">
                        {{ $contentType === ContentType::ARTICLE ? 'Resumo' : 'Descrição' }}
                    </label>
                    <div class="mt-1">
                        <textarea wire:model="excerpt"
                                  id="excerpt"
                                  rows="3"
                                  placeholder="{{ $placeholders['excerpt'] ?? 'Breve descrição...' }}"
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm"></textarea>
                    </div>
                    @error('excerpt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- Conteúdo Principal (apenas para artigos e descrição longa) --}}
                @if($visibleFields['content'] && $contentType === ContentType::ARTICLE)
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">
                        Conteúdo 
                        @if($contentType->isRequiredForPublishing('content'))
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <div class="mt-1">
                        {{-- Aqui você integraria seu editor rico (TinyMCE, CKEditor, etc) --}}
                        <textarea wire:model="content"
                                  id="content"
                                  rows="20"
                                  placeholder="{{ $placeholders['content'] ?? 'Escreva seu conteúdo aqui...' }}"
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm"></textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Tempo estimado de leitura: {{ $reading_time ?? '?' }} min
                    </p>
                    @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>

            {{-- Seção de Mídia --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Mídia</h2>

                {{-- Imagem de Destaque --}}
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Imagem de Destaque
                        @if($contentType->mediaRequirements()['featured_image'] === 'required')
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Preview da imagem atual --}}
                        @if($current_featured_image)
                        <div class="relative group">
                            <img src="{{ $current_featured_image['url'] }}" 
                                 alt="Imagem de destaque"
                                 class="w-full h-48 object-cover rounded-lg border-2 border-gray-200">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <button type="button"
                                        wire:click="removeFeaturedImage"
                                        wire:confirm="Remover imagem de destaque?"
                                        class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 focus:outline-none">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Imagem atual</p>
                        </div>
                        @endif

                        {{-- Upload nova imagem --}}
                        <div class="{{ $current_featured_image ? '' : 'lg:col-span-2' }}">
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-{{ $contentType->color() }}-300 transition-colors">
                                <div class="space-y-2 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="featured_image" class="relative cursor-pointer bg-white rounded-md font-medium text-{{ $contentType->color() }}-600 hover:text-{{ $contentType->color() }}-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-{{ $contentType->color() }}-500">
                                            <span>Upload nova imagem</span>
                                            <input id="featured_image" 
                                                   type="file" 
                                                   wire:model="featured_image"
                                                   class="sr-only"
                                                   accept="image/jpeg,image/png,image/webp">
                                        </label>
                                        <p class="pl-1">ou arraste</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, WebP até 5MB • Mínimo 800x400px
                                    </p>
                                    @if($featured_image)
                                        <div class="mt-3 p-2 bg-green-50 rounded-lg">
                                            <p class="text-sm text-green-600">
                                                ✓ {{ $featured_image->getClientOriginalName() }}
                                            </p>
                                            {{-- <button type="button"
                                                    wire:click="uploadFeaturedImage"
                                                    class="mt-2 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-{{ $contentType->color() }}-600 hover:bg-{{ $contentType->color() }}-700">
                                                Fazer Upload
                                            </button> --}}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('featured_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Galeria de Imagens (apenas para artigos) --}}
                @if($visibleFields['gallery'])
                <div class="mb-8 border-t border-gray-200 pt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Galeria de Imagens
                        <span class="text-xs text-gray-500 ml-2">(Opcional)</span>
                    </label>

                    @if(!empty($current_gallery))
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4">
                        @foreach($current_gallery as $index => $image)
                        <div class="relative group">
                            <img src="{{ $image['url'] }}" 
                                 alt="Galeria"
                                 class="w-full h-24 object-cover rounded-lg border">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <button type="button"
                                        wire:click="removeGalleryImage({{ $image['id'] }})"
                                        wire:confirm="Remover esta imagem?"
                                        class="bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <span class="absolute top-1 left-1 bg-gray-900 bg-opacity-75 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="gallery_images" class="relative cursor-pointer bg-white rounded-md font-medium text-{{ $contentType->color() }}-600 hover:text-{{ $contentType->color() }}-500">
                                    <span>Upload múltiplas imagens</span>
                                    <input id="gallery_images" 
                                           type="file" 
                                           wire:model="gallery_images"
                                           class="sr-only"
                                           multiple
                                           accept="image/jpeg,image/png,image/webp">
                                </label>
                                <p class="pl-1">ou arraste</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, WebP até 5MB cada
                            </p>
                            @if(!empty($gallery_images))
                                <div class="mt-3 p-3 bg-green-50 rounded-lg text-left">
                                    <p class="text-sm text-green-600 mb-2">
                                        {{ count($gallery_images) }} arquivo(s) selecionado(s):
                                    </p>
                                    <ul class="text-xs text-gray-600 list-disc list-inside max-h-32 overflow-y-auto">
                                        @foreach($gallery_images as $image)
                                            <li>{{ $image->getClientOriginalName() }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button"
                                            wire:click="uploadGalleryImages"
                                            class="mt-3 w-full inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-{{ $contentType->color() }}-600 hover:bg-{{ $contentType->color() }}-700">
                                        Upload imagens
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    @error('gallery_images.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- PDF (para jornais) --}}
                @if($visibleFields['pdf'])
                <div class="border-t border-gray-200 pt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Arquivo PDF da Edição
                        @if($contentType->mediaRequirements()['pdf'] === 'required')
                            <span class="text-red-500">*</span>
                        @endif
                    </label>

                    @if($current_pdf)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <a href="{{ $current_pdf['url'] }}" target="_blank" class="text-sm font-medium text-{{ $contentType->color() }}-600 hover:text-{{ $contentType->color() }}-800">
                                    {{ $current_pdf['name'] }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $current_pdf['file_name'] }} ({{ $current_pdf['size'] }})</p>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="removePdf"
                                wire:confirm="Remover este PDF?"
                                class="text-red-600 hover:text-red-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    @endif

                    <div class="mt-1">
                        <input type="file" 
                               wire:model="pdf"
                               accept=".pdf,application/pdf"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-{{ $contentType->color() }}-50 file:text-{{ $contentType->color() }}-700 hover:file:bg-{{ $contentType->color() }}-100">
                    </div>
                    @error('pdf') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>

            {{-- Categorias e Tags --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Organização</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Categorias --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Categorias</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto p-3 border rounded-lg">
                            @forelse($categories as $category)
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg">
                                    <input type="checkbox" 
                                           wire:model="selectedCategories" 
                                           value="{{ $category->id }}"
                                           class="rounded border-gray-300 text-{{ $contentType->color() }}-600 shadow-sm focus:ring-{{ $contentType->color() }}-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">
                                    Nenhuma categoria cadastrada
                                </p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Tags --}}
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Tags</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto p-3 border rounded-lg">
                            @forelse($tags as $tag)
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg">
                                    <input type="checkbox" 
                                           wire:model="selectedTags" 
                                           value="{{ $tag->id }}"
                                           class="rounded border-gray-300 text-{{ $contentType->color() }}-600 shadow-sm focus:ring-{{ $contentType->color() }}-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">
                                    Nenhuma tag cadastrada
                                </p>
                            @endforelse
                        </div>
                    </div> --}}
                </div>
            </div>

            {{-- Status e Publicação --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Publicação</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($statuses as $statusOption)
                                <button type="button"
                                        wire:click="$set('status', '{{ $statusOption['value'] }}')"
                                        class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors
                                            {{ $status === $statusOption['value'] 
                                                ? 'bg-' . $statusOption['color'] . '-100 border-' . $statusOption['color'] . '-500 text-' . $statusOption['color'] . '-700' 
                                                : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                    {{ $statusOption['label'] }}
                                </button>
                            @endforeach
                        </div>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Data de Publicação --}}
                    <div>
                        <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Data de Publicação
                            @if($status === 'scheduled')
                                <span class="text-yellow-500">*</span>
                            @endif
                        </label>
                        <input type="datetime-local" 
                               wire:model="published_at" 
                               id="published_at"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm">
                        @error('published_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SEO (Opcional) --}}
            <div class="bg-white shadow-sm rounded-lg">
                <button type="button"
                        wire:click="$toggle('showSeo')"
                        class="w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none">
                    <span class="text-lg font-medium text-gray-900">SEO (Opcional)</span>
                    <svg class="h-5 w-5 text-gray-500 transform transition-transform {{ $showSeo ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                @if($showSeo)
                <div class="px-6 pb-6 space-y-4 border-t border-gray-200 pt-4">
                    <div>
                        <label for="seo_title" class="block text-sm font-medium text-gray-700">
                            Título SEO
                        </label>
                        <input type="text" 
                               wire:model="seo_title" 
                               id="seo_title"
                               maxlength="70"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">{{ strlen($seo_title ?? '') }}/70 caracteres</p>
                    </div>

                    <div>
                        <label for="seo_description" class="block text-sm font-medium text-gray-700">
                            Descrição SEO
                        </label>
                        <textarea wire:model="seo_description"
                                  id="seo_description"
                                  rows="2"
                                  maxlength="160"
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm"></textarea>
                        <p class="mt-1 text-xs text-gray-500">{{ strlen($seo_description ?? '') }}/160 caracteres</p>
                    </div>

                    <div>
                        <label for="seo_keywords" class="block text-sm font-medium text-gray-700">
                            Palavras-chave
                        </label>
                        <input type="text" 
                               wire:model="seo_keywords" 
                               id="seo_keywords"
                               placeholder="separadas por vírgula"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-{{ $contentType->color() }}-500 focus:border-{{ $contentType->color() }}-500 sm:text-sm">
                    </div>
                </div>
                @endif
            </div>

            {{-- Botões de Ação --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.articles.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $contentType->color() }}-500">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-{{ $contentType->color() }}-600 hover:bg-{{ $contentType->color() }}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $contentType->color() }}-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    {{ $article ? 'Atualizar' : 'Salvar' }}
                </button>
            </div>
        </form>
        
        {{-- Notificações Toast --}}
        <div x-data="{ show: false, message: '', type: 'success' }"
             @notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 5000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed bottom-4 right-4 z-50">
            <div x-bind:class="{
                'bg-green-500': type === 'success',
                'bg-red-500': type === 'error',
                'bg-blue-500': type === 'info',
                'bg-yellow-500': type === 'warning'
            }" class="text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
                <template x-if="type === 'success'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </template>
                <template x-if="type === 'error'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </template>
                <template x-if="type === 'info'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </template>
                <template x-if="type === 'warning'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </template>
                <span x-text="message"></span>
            </div>
        </div>

        {{-- Modal de Confirmação para YouTube Thumbnail --}}
        <div x-data="{ show: false }"
             @confirm-use-youtube-thumbnail.window="show = true"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Usar thumbnail do YouTube?
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Deseja usar a thumbnail oficial do YouTube como imagem de destaque? 
                                        Você poderá substituí-la manualmente depois.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                wire:click="useYouTubeThumbnail"
                                @click="show = false"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Sim, usar thumbnail
                        </button>
                        <button type="button"
                                @click="show = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Não, obrigado
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading Overlay --}}
        <div wire:loading.delay class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                <svg class="animate-spin h-6 w-6 text-{{ $contentType->color() }}-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700">Salvando...</span>
            </div>
        </div>
    </div>
</div>

{{-- Scripts para integração com editor rich text --}}
@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Aqui você pode inicializar seu editor rich text (TinyMCE, CKEditor, etc)
        // Exemplo com TinyMCE:
        /*
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
            setup: function(editor) {
                editor.on('change', function() {
                    Livewire.dispatch('contentUpdated', editor.getContent());
                });
            }
        });
        */

        // Listen para mudanças de tipo
        Livewire.on('type-changed', (type) => {
            console.log('Tipo alterado para:', type);
            // Aqui você pode ajustar configurações do editor baseado no tipo
        });
    });
</script>
@endpush

{{-- Estilos adicionais --}}
@push('styles')
<style>
    /* Animações suaves */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    /* Scrollbar personalizada */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Hover effects para uploads */
    .border-dashed:hover {
        border-color: #9ca3af;
    }
    
    /* Loading spinner animation */
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
</style>
@endpush