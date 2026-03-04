<?php
// app/Domains/Content/Http/Livewire/Admin/ArticleForm.php

namespace App\Domains\Content\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Category;
use App\Domains\Content\Enums\ContentStatus;
use App\Domains\Content\Enums\ContentType;
use App\Domains\Media\Services\MediaService;
use App\Domains\Media\Enums\MediaCollectionType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArticleForm extends Component
{
    use WithFileUploads;

    public ?Article $article = null;
    
    // Dados do formulário
    public string $type = 'article';
    public string $title = '';
    public string $slug = '';
    public ?string $excerpt = null;
    public ?string $content = null;
    public ?string $youtube_url = null;
    public ?string $edition = null;
    public string $status = 'draft';
    public ?string $published_at = null;
    public array $selectedCategories = [];
    
    // Upload de imagem simplificado
    public $featured_image_temp = null; // Arquivo temporário do Livewire
    public ?string $featured_image_url = null; // URL da imagem atual/preview
    public ?int $featured_image_id = null; // ID da mídia atual para referência
    
    // Controle de UI
    public bool $isEditing = false;
    public bool $showPreview = false;
    public bool $removeCurrentImage = false;

    protected MediaService $mediaService;

    public function boot(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    protected function rules()
    {
        $rules = [
            'type' => 'required|in:article,video,newspaper',
            'title' => 'required|min:3|max:255',
            'slug' => 'required|max:255|unique:articles,slug,' . ($this->article?->id ?? 'NULL'),
            'excerpt' => 'nullable|max:500',
            'status' => 'required|in:draft,published,scheduled',
            'selectedCategories' => 'array',
        ];

        // Regras específicas por tipo
        if ($this->type === 'article') {
            $rules['content'] = 'required|min:10';
        } elseif ($this->type === 'video') {
            $rules['youtube_url'] = 'required|url|regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/';
        } elseif ($this->type === 'newspaper') {
            $rules['edition'] = 'required|max:100';
        }

        // Regra para imagem - apenas valida se um novo arquivo foi enviado
        if ($this->featured_image_temp) {
            $rules['featured_image_temp'] = 'image|max:5120'; // 5MB max
        }

        return $rules;
    }

    protected $messages = [
        'featured_image_temp.image' => 'O arquivo deve ser uma imagem válida (JPG, PNG, WebP)',
        'featured_image_temp.max' => 'A imagem não pode ter mais que 5MB',
        'youtube_url.regex' => 'Por favor, insira uma URL válida do YouTube',
        'edition.required' => 'O número da edição é obrigatório para jornais',
    ];

    public function mount(?Article $article = null): void
    {
        $this->article = $article;
        $this->isEditing = $article !== null;

        if ($article) {
            $this->type = $article->type->value;
            $this->title = $article->title;
            $this->slug = $article->slug;
            $this->excerpt = $article->excerpt;
            $this->content = $article->content;
            $this->youtube_url = $article->youtube_url;
            $this->edition = $article->edition;
            $this->status = $article->status->value;
            $this->published_at = $article->published_at?->format('Y-m-d\TH:i');
            $this->selectedCategories = $article->categories->pluck('id')->toArray();
            
            // Carregar imagem atual da Media Library
            if ($article->hasFeaturedImage()) {
                $media = $article->getFirstMedia(MediaCollectionType::FEATURED_IMAGE->value);
                $this->featured_image_url = $article->getFeaturedImageUrl('preview');
                $this->featured_image_id = $media?->id;
            }
        } else {
            $this->published_at = now()->format('Y-m-d\TH:i');
        }
    }

    public function updatedTitle(): void
    {
        if (!$this->isEditing || empty($this->slug) || $this->slug === Str::slug($this->title)) {
            $this->generateSlug();
        }
    }

    public function generateSlug(): void
    {
        $this->slug = Str::slug($this->title);
    }

    public function updatedFeaturedImageTemp()
    {
        $this->validateOnly('featured_image_temp');
        
        // Criar preview temporário do Livewire
        if ($this->featured_image_temp) {
            $this->featured_image_url = $this->featured_image_temp->temporaryUrl();
            $this->removeCurrentImage = false; // Reset flag de remoção
        }
    }

    public function removeFeaturedImage()
    {
        $this->featured_image_temp = null;
        
        if ($this->isEditing && $this->article?->hasFeaturedImage()) {
            // Marcar para remoção no save
            $this->removeCurrentImage = true;
            $this->featured_image_url = null;
            $this->featured_image_id = null;
        } else {
            $this->featured_image_url = null;
            $this->featured_image_id = null;
        }
    }

    public function updatedType($value)
    {
        // Limpar campos específicos ao mudar o tipo
        if ($value !== 'video') {
            $this->youtube_url = null;
        }
        if ($value !== 'newspaper') {
            $this->edition = null;
        }
        if ($value !== 'article') {
            $this->content = null;
        }
    }

    public function save()
    {
        $this->validate();

        // Preparar dados
        $data = [
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->type === 'article' ? $this->content : null,
            'youtube_url' => $this->type === 'video' ? $this->youtube_url : null,
            'edition' => $this->type === 'newspaper' ? $this->edition : null,
            'status' => $this->status,
            'published_at' => $this->status === 'scheduled' ? $this->published_at : now(),
            'author_id' => Auth::id(),
        ];

        try {
            DB::transaction(function () use ($data) {
                if ($this->isEditing) {
                    // Atualizar artigo
                    $this->article->update($data);
                    
                    // Gerenciar imagem de destaque
                    $this->handleFeaturedImage($this->article);
                    
                } else {
                    // Criar artigo
                    $this->article = Article::create($data);
                    
                    // Gerenciar imagem de destaque
                    $this->handleFeaturedImage($this->article);
                }
                
                // Sincronizar categorias
                $this->article->categories()->sync($this->selectedCategories);
            });

            session()->flash('success', $this->isEditing ? 'Artigo atualizado com sucesso!' : 'Artigo criado com sucesso!');
            
            return $this->isEditing 
                ? redirect()->route('admin.articles.index')
                : redirect()->route('admin.articles.edit', $this->article);

        } catch (\Exception $e) {
            Log::error('Erro ao salvar artigo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Erro ao salvar: ' . $e->getMessage());
        }
    }

    /**
     * Gerenciar upload/remoção da imagem de destaque usando MediaService
     */
    protected function handleFeaturedImage(Article $article): void
    {
        // Caso 1: Remover imagem existente (marcado para remoção)
        if ($this->removeCurrentImage && $this->featured_image_id) {
            $this->mediaService->remove($article, $this->featured_image_id);
            $this->featured_image_id = null;
            $this->featured_image_url = null;
            $this->removeCurrentImage = false;
        }

        // Caso 2: Upload de nova imagem
        if ($this->featured_image_temp) {
            try {
                // Se já tem imagem, substitui (media service lida com isso)
                $media = $this->mediaService->upload(
                    model: $article,
                    file: $this->featured_image_temp,
                    collection: MediaCollectionType::FEATURED_IMAGE,
                    customName: "featured-{$article->slug}-" . now()->format('Y-m-d'),
                    customProperties: [
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now()->toDateTimeString(),
                        'article_type' => $this->type,
                    ]
                );

                // Atualizar referências
                $this->featured_image_id = $media->id;
                $this->featured_image_url = $media->getUrl('preview');
                
                // Limpar temp
                $this->featured_image_temp = null;

            } catch (\Exception $e) {
                Log::error('Erro no upload da imagem', [
                    'article_id' => $article->id,
                    'error' => $e->getMessage()
                ]);
                
                throw new \Exception('Falha ao fazer upload da imagem: ' . $e->getMessage());
            }
        }
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    /**
     * Regras de validação em tempo real
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.admin.article-form', [
            'categories' => Category::orderBy('name')->get(),
            'contentTypes' => [
                ['value' => 'article', 'label' => 'Artigo', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                ['value' => 'video', 'label' => 'Vídeo', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                ['value' => 'newspaper', 'label' => 'Jornal', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9'],
            ],
            'statusOptions' => [
                ['value' => 'draft', 'label' => 'Rascunho', 'color' => 'gray'],
                ['value' => 'published', 'label' => 'Publicar', 'color' => 'green'],
                ['value' => 'scheduled', 'label' => 'Agendar', 'color' => 'yellow'],
            ],
        ]);
    }
}