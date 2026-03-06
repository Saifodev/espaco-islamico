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

    // Upload de imagem
    public $featured_image_temp = null;
    public ?string $featured_image_url = null;
    public ?int $featured_image_id = null;

    // Upload de PDF para newspaper
    public $pdf_temp = null;
    public ?string $pdf_url = null;
    public ?string $pdf_name = null;
    public ?int $pdf_id = null;
    public bool $removeCurrentPdf = false;

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
            $rules['youtube_url'] = [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    if (!str_contains($value, 'youtube.com') && !str_contains($value, 'youtu.be')) {
                        $fail('A URL deve ser do YouTube.');
                    }
                }
            ];
        } elseif ($this->type === 'newspaper') {
            $rules['edition'] = 'required|max:100';
            
            // Regras para PDF apenas se um novo arquivo foi enviado
            if ($this->pdf_temp) {
                $rules['pdf_temp'] = 'file|mimes:pdf|max:20480'; // 20MB max
            }
        }

        // Regra para imagem
        if ($this->featured_image_temp) {
            $rules['featured_image_temp'] = 'image|max:5120';
        }

        return $rules;
    }

    protected $messages = [
        'featured_image_temp.image' => 'O arquivo deve ser uma imagem válida (JPG, PNG, WebP)',
        'featured_image_temp.max' => 'A imagem não pode ter mais que 5MB',
        'youtube_url.regex' => 'Por favor, insira uma URL válida do YouTube',
        'edition.required' => 'O número da edição é obrigatório para jornais',
        'pdf_temp.mimes' => 'O arquivo deve ser um PDF válido',
        'pdf_temp.max' => 'O PDF não pode ter mais que 20MB',
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

            // Carregar imagem atual
            if ($article->hasFeaturedImage()) {
                $media = $article->getFirstMedia(MediaCollectionType::FEATURED_IMAGE->value);
                $this->featured_image_url = $article->getFeaturedImageUrl();
                $this->featured_image_id = $media?->id;
            }

            // Carregar PDF atual para newspaper
            if ($this->type === 'newspaper' && $article->hasMedia('documents')) {
                $pdf = $article->getFirstMedia('documents');
                if ($pdf) {
                    $this->pdf_url = $pdf->getUrl();
                    $this->pdf_name = $pdf->file_name;
                    $this->pdf_id = $pdf->id;
                }
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
        
        if ($this->featured_image_temp) {
            $this->featured_image_url = $this->featured_image_temp->temporaryUrl();
            $this->removeCurrentImage = false;
        }
    }

    public function updatedPdfTemp()
    {
        $this->validateOnly('pdf_temp');
        
        // Quando um novo PDF é selecionado, marcamos para remover o antigo no save
        if ($this->pdf_temp && $this->pdf_id) {
            $this->removeCurrentPdf = true;
        }
    }

    public function removeFeaturedImage()
    {
        $this->featured_image_temp = null;

        if ($this->isEditing && $this->article?->hasFeaturedImage()) {
            $this->removeCurrentImage = true;
            $this->featured_image_url = null;
            $this->featured_image_id = null;
        } else {
            $this->featured_image_url = null;
            $this->featured_image_id = null;
        }
    }

    public function removePdf()
    {
        $this->pdf_temp = null;

        if ($this->isEditing && $this->pdf_id) {
            $this->removeCurrentPdf = true;
            $this->pdf_url = null;
            $this->pdf_name = null;
            $this->pdf_id = null;
        } else {
            $this->pdf_url = null;
            $this->pdf_name = null;
            $this->pdf_id = null;
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
            $this->pdf_temp = null;
            $this->pdf_url = null;
            $this->pdf_name = null;
            $this->pdf_id = null;
            $this->removeCurrentPdf = false;
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
            'content' => $this->type === 'article' ? $this->content : '',
            'youtube_url' => $this->type === 'video' ? $this->youtube_url : null,
            'edition' => $this->type === 'newspaper' ? $this->edition : null,
            'status' => $this->status,
            'published_at' => $this->status === 'scheduled' ? $this->published_at : now(),
            'author_id' => Auth::id(),
        ];

        try {
            DB::transaction(function () use ($data) {
                if ($this->isEditing) {
                    $this->article->update($data);
                    $this->handleFeaturedImage($this->article);
                    $this->handlePdf($this->article);
                } else {
                    $this->article = Article::create($data);
                    $this->handleFeaturedImage($this->article);
                    $this->handlePdf($this->article);
                }

                // Sincronizar categorias
                $this->article->categories()->sync($this->selectedCategories);
            });

            session()->flash('success', $this->isEditing ? 'Artigo atualizado com sucesso!' : 'Artigo criado com sucesso!');

            // return $this->isEditing
            //     ? redirect()->route('admin.articles.index')
            //     : redirect()->route('admin.articles.edit', $this->article);
            return redirect()->route('admin.articles.show', $this->article->id);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar artigo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Erro ao salvar: ' . $e->getMessage());
        }
    }

    /**
     * Gerenciar upload/remoção da imagem de destaque
     */
    protected function handleFeaturedImage(Article $article): void
    {
        // Remover imagem existente
        if ($this->removeCurrentImage && $this->featured_image_id) {
            $this->mediaService->remove($article, $this->featured_image_id);
            $this->featured_image_id = null;
            $this->featured_image_url = null;
            $this->removeCurrentImage = false;
        }

        // Upload de nova imagem
        if ($this->featured_image_temp) {
            try {
                $media = $this->mediaService->upload(
                    model: $article,
                    file: $this->featured_image_temp,
                    collection: MediaCollectionType::FEATURED_IMAGE,
                    customName: "featured-{$article->slug}-" . now()->format('Y-m-d'),
                    customProperties: [
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now()->toDateTimeString(),
                        'article_type' => $this->type,
                    ],
                    preserveOriginal: true
                );

                $this->featured_image_id = $media->id;
                $this->featured_image_url = $media->getUrl();
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

    /**
     * Gerenciar upload/remoção do PDF para newspaper
     */
    protected function handlePdf(Article $article): void
    {
        // Só processa PDF se for do tipo newspaper
        if ($this->type !== 'newspaper') {
            return;
        }

        // Remover PDF existente
        if ($this->removeCurrentPdf && $this->pdf_id) {
            $this->mediaService->remove($article, $this->pdf_id);
            $this->pdf_id = null;
            $this->pdf_url = null;
            $this->pdf_name = null;
            $this->removeCurrentPdf = false;
        }

        // Upload de novo PDF
        if ($this->pdf_temp) {
            try {
                $media = $this->mediaService->upload(
                    model: $article,
                    file: $this->pdf_temp,
                    collection: MediaCollectionType::DOCUMENTS,
                    customName: "newspaper-{$article->slug}-edition-{$this->edition}",
                    customProperties: [
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now()->toDateTimeString(),
                        'edition' => $this->edition,
                        'article_type' => $this->type,
                    ],
                    preserveOriginal: true
                );

                $this->pdf_id = $media->id;
                $this->pdf_url = $media->getUrl();
                $this->pdf_name = $media->file_name;
                $this->pdf_temp = null;
            } catch (\Exception $e) {
                Log::error('Erro no upload do PDF', [
                    'article_id' => $article->id,
                    'error' => $e->getMessage()
                ]);
                throw new \Exception('Falha ao fazer upload do PDF: ' . $e->getMessage());
            }
        }
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'featured_image_temp' || $propertyName === 'pdf_temp') {
            return;
        }
        $this->validateOnly($propertyName);
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function render()
    {
        return view('livewire.admin.article-form', [
            'categories' => Category::orderBy('name')->get(),
            'contentTypes' => [
                ['value' => 'article', 'label' => 'Artigo'],
                ['value' => 'video', 'label' => 'Vídeo'],
                ['value' => 'newspaper', 'label' => 'Jornal'],
            ],
            'statusOptions' => [
                ['value' => 'draft', 'label' => 'Rascunho'],
                ['value' => 'published', 'label' => 'Publicar'],
                ['value' => 'scheduled', 'label' => 'Agendar'],
            ],
        ]);
    }
}