<?php
// app/Domains/Content/Http/Livewire/Admin/ArticleForm.php

namespace App\Domains\Content\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Category;
use App\Domains\Content\Models\Tag;
use App\Domains\Content\Enums\ContentStatus;
use App\Domains\Content\Enums\ContentType;
use App\Domains\Content\Services\ArticleService;
use App\Domains\Media\Actions\UploadFeaturedImageAction;
use App\Domains\Media\Actions\UploadGalleryImagesAction;
use App\Domains\Media\Actions\UploadPdfAction;
use App\Domains\Media\Actions\RemoveMediaAction;
use App\Domains\Media\Enums\MediaCollectionType;
use App\Domains\Media\Services\MediaService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ArticleForm extends Component
{
    use WithFileUploads;

    public ?Article $article = null;

    // Campos do formulário
    public string $type = 'article';
    public string $title = '';
    public string $slug = '';
    public ?string $excerpt = null;
    public ?string $content = null;
    public ?string $youtube_url = null;
    public ?string $edition = null;
    public string $status = 'draft';
    public ?string $published_at = null;
    public ?string $seo_title = null;
    public ?string $seo_description = null;
    public ?string $seo_keywords = null;
    public ?int $reading_time = null;
    public array $selectedCategories = [];
    public array $selectedTags = [];

    // Uploads de mídia
    public $featured_image;
    public array $gallery_images = [];
    public $pdf;

    // Preview de mídia existente
    public ?array $current_featured_image = null;
    public array $current_gallery = [];
    public ?array $current_pdf = null;

    // UI State
    public bool $showSeo = false;
    public bool $showAdvanced = false;
    public ?string $confirmingTypeChange = null;
    public array $validationErrors = [];

    protected $listeners = [
        'contentUpdated' => 'updateContent',
        'refreshMedia' => '$refresh',
    ];

    public function rules(): array
    {
        // As regras são dinâmicas baseadas no tipo e status
        $type = ContentType::tryFrom($this->type) ?? ContentType::ARTICLE;
        $isPublishing = $this->status === ContentStatus::PUBLISHED->value;

        return $type->validationRules($isPublishing);
    }

    public function messages(): array
    {
        $type = ContentType::tryFrom($this->type) ?? ContentType::ARTICLE;

        return match ($type) {
            ContentType::VIDEO => [
                'youtube_url.required' => 'A URL do YouTube é obrigatória para vídeos',
                'youtube_url.regex' => 'Por favor, insira uma URL válida do YouTube',
            ],
            ContentType::NEWSPAPER => [
                'edition.required' => 'O número da edição é obrigatório para jornais',
            ],
            default => [
                'content.required' => 'O conteúdo é obrigatório para artigos',
            ],
        };
    }

    public function mount(?Article $article = null): void
    {
        $this->article = $article;

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
            $this->seo_title = $article->seo_title;
            $this->seo_description = $article->seo_description;
            $this->seo_keywords = $article->seo_keywords;
            $this->reading_time = $article->reading_time;
            $this->selectedCategories = $article->categories->pluck('id')->toArray();
            $this->selectedTags = $article->tags->pluck('id')->toArray();

            $this->loadExistingMedia();
        } else {
            // Carregar mídia temporária da sessão se existir
            $this->loadTempMedia();
        }
    }

    protected function loadExistingMedia(): void
    {
        // if (!$this->article) return;

        // // Imagem de destaque
        // $featuredImage = $this->article->getFirstMedia(MediaCollectionType::FEATURED_IMAGE->value);
        // if ($featuredImage) {
        //     $this->current_featured_image = [
        //         'id' => $featuredImage->id,
        //         'url' => $featuredImage->getUrl('thumb'),
        //         'original_url' => $featuredImage->getUrl(),
        //         'name' => $featuredImage->name,
        //     ];
        // }

        // // Galeria
        // $this->current_gallery = $this->article->getMedia(MediaCollectionType::GALLERY->value)
        //     ->map(fn($media) => [
        //         'id' => $media->id,
        //         'url' => $media->getUrl('thumb'),
        //         'original_url' => $media->getUrl('preview'),
        //         'name' => $media->name,
        //     ])
        //     ->toArray();

        // // PDF (para jornais)
        // $pdf = $this->article->getFirstMedia('pdf');
        // if ($pdf) {
        //     $this->current_pdf = [
        //         'id' => $pdf->id,
        //         'name' => $pdf->name,
        //         'file_name' => $pdf->file_name,
        //         'size' => $this->formatBytes($pdf->size),
        //         'url' => $pdf->getUrl(),
        //     ];
        // }
    }

    protected function loadTempMedia(): void
    {
        if (session()->has('temp_featured_image')) {
            // Não podemos mostrar preview de temp, apenas indicar que há
            $this->dispatch('notify', [
                'message' => 'Há uma imagem temporária aguardando salvamento',
                'type' => 'info'
            ]);
        }
    }

    public function updatedType($oldType, $newType): void
    {
        if ($this->article && $this->hasData()) {
            $this->confirmingTypeChange = $newType;
            $this->type = $oldType; // Reverter temporariamente
            return;
        }

        $this->resetTypeSpecificFields($newType);
    }

    public function changeType(string $newType): void
    {
        $oldType = $this->type;

        if ($this->article && $this->hasData()) {
            $this->confirmingTypeChange = $newType;
            return;
        }

        $this->type = $newType;
        $this->resetTypeSpecificFields($newType);
    }

    protected function resetTypeSpecificFields(string $newType): void
    {
        $type = ContentType::tryFrom($newType) ?? ContentType::ARTICLE;
        $visibleFields = $type->visibleFields();

        if (!$visibleFields['youtube_url']) {
            $this->youtube_url = null;
        }

        if (!$visibleFields['edition']) {
            $this->edition = null;
        }

        if (!$visibleFields['gallery']) {
            $this->gallery_images = [];
            $this->current_gallery = [];
        }

        if ($type !== ContentType::ARTICLE && $this->content) {
            // Manter como descrição, não limpar
        }

        $this->dispatch('type-changed', $newType);
    }

    protected function hasData(): bool
    {
        return !empty($this->title) ||
            !empty($this->content) ||
            !empty($this->youtube_url) ||
            !empty($this->edition) ||
            $this->current_featured_image ||
            !empty($this->current_gallery) ||
            $this->current_pdf;
    }

    public function confirmTypeChange(): void
    {
        $newType = $this->confirmingTypeChange;
        $this->type = $newType;
        $this->resetTypeSpecificFields($newType);
        $this->confirmingTypeChange = null;

        $this->dispatch('notify', [
            'message' => 'Tipo alterado com sucesso. Verifique os campos específicos.',
            'type' => 'success'
        ]);
    }

    public function cancelTypeChange(): void
    {
        $this->confirmingTypeChange = null;
    }

    public function updatedTitle(): void
    {
        if (empty($this->slug) || $this->slug === str($this->title)->slug()) {
            $this->generateSlug();
        }
    }

    public function generateSlug(): void
    {
        $this->slug = str($this->title)->slug();
    }

    public function updatedYoutubeUrl(): void
    {
        // Se não tem imagem de destaque, sugerir usar thumbnail do YouTube
        if (!$this->current_featured_image && $this->youtube_url) {
            $this->dispatch('confirm-use-youtube-thumbnail');
        }
    }

    public function useYouTubeThumbnail(): void
    {
        $article = $this->article;
        if (!$article || !$article->youtube_thumbnail) {
            return;
        }

        // Aqui você implementaria a lógica para baixar e salvar a thumbnail
        $this->dispatch('notify', [
            'message' => 'Thumbnail do YouTube será baixada ao salvar',
            'type' => 'info'
        ]);

        session()->put('use_youtube_thumbnail', true);
    }

    public function uploadFeaturedImage(): void
    {
        $this->validateOnly('featured_image');

        try {
            if (!$this->article) {
                // session()->put('temp_featured_image', $this->featured_image);
                $this->dispatch('notify', [
                    'message' => 'Imagem carregada temporariamente. Salve o artigo para finalizar.',
                    'type' => 'info'
                ]);
                $this->featured_image = null;
                return;
            }

            $action = app(UploadFeaturedImageAction::class);
            $action->execute($this->article, $this->featured_image);

            $this->featured_image = null;
            $this->loadExistingMedia();

            $this->dispatch('notify', [
                'message' => 'Imagem de destaque atualizada com sucesso!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erro ao fazer upload: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function uploadGalleryImages(): void
    {
        $this->validateOnly('gallery_images');

        try {
            if (empty($this->gallery_images)) return;

            if (!$this->article) {
                // session()->put('temp_gallery_images', $this->gallery_images);
                $this->dispatch('notify', [
                    'message' => 'Imagens carregadas temporariamente. Salve o artigo para finalizar.',
                    'type' => 'info'
                ]);
                $this->gallery_images = [];
                return;
            }

            $action = app(UploadGalleryImagesAction::class);
            $action->execute($this->article, $this->gallery_images);

            $this->gallery_images = [];
            $this->loadExistingMedia();

            $this->dispatch('notify', [
                'message' => 'Imagens adicionadas à galeria!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erro ao fazer upload: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function uploadPdf(): void
    {
        $this->validateOnly('pdf');

        try {
            if (!$this->article) {
                // session()->put('temp_pdf', $this->pdf);
                $this->dispatch('notify', [
                    'message' => 'PDF carregado temporariamente. Salve o artigo para finalizar.',
                    'type' => 'info'
                ]);
                $this->pdf = null;
                return;
            }

            $action = app(UploadPdfAction::class);
            $action->execute($this->article, $this->pdf);

            $this->pdf = null;
            $this->loadExistingMedia();

            $this->dispatch('notify', [
                'message' => 'PDF atualizado com sucesso!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erro ao fazer upload: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function removeFeaturedImage(): void
    {
        try {
            if (!$this->article?->hasFeaturedImage()) return;

            $mediaId = $this->article->getFirstMedia(MediaCollectionType::FEATURED_IMAGE->value)->id;

            $action = app(RemoveMediaAction::class);
            $action->execute($this->article, $mediaId);

            $this->current_featured_image = null;

            $this->dispatch('notify', [
                'message' => 'Imagem de destaque removida!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erro ao remover imagem: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function removeGalleryImage(int $mediaId): void
    {
        try {
            $action = app(RemoveMediaAction::class);
            $action->execute($this->article, $mediaId);

            $this->loadExistingMedia();

            $this->dispatch('notify', [
                'message' => 'Imagem removida da galeria!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erro ao remover imagem: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function removePdf(): void
    {
        try {
            if (!$this->article?->hasMedia('pdf')) return;

            $mediaId = $this->article->getFirstMedia('pdf')->id;

            $action = app(RemoveMediaAction::class);
            $action->execute($this->article, $mediaId);

            $this->current_pdf = null;

            $this->dispatch('notify', [
                'message' => 'PDF removido!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erro ao remover PDF: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function updateContent(string $content): void
    {
        $this->content = $content;

        // Atualizar tempo de leitura se for artigo
        if ($this->type === ContentType::ARTICLE->value) {
            $wordCount = str_word_count(strip_tags($content));
            $this->reading_time = max(1, ceil($wordCount / 200));
        }
    }

    public function save()
    {
        $this->validate();

        // Verificar se pode publicar
        if ($this->status === ContentStatus::PUBLISHED->value) {
            $type = ContentType::tryFrom($this->type) ?? ContentType::ARTICLE;

            if ($this->article) {
                $errors = $this->article->getPublishErrors();
            } else {
                $errors = $this->validatePublishingRequirements($type);
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addError('publish', $error);
                }
                return;
            }
        }

        $data = [
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'youtube_url' => $this->youtube_url,
            'edition' => $this->edition,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'reading_time' => $this->reading_time,
            'categories' => $this->selectedCategories,
            'tags' => $this->selectedTags,
        ];

        $service = App::make(ArticleService::class);

        try {
            if ($this->article) {
                $this->article = $service->update($this->article, $data);
                // $this->processTempMedia();
                $message = 'Artigo atualizado com sucesso!';
            } else {
                $this->article = $service->create($data, Auth::user());
                // $this->processTempMedia();
                $message = 'Artigo criado com sucesso!';
            }

            if ($this->featured_image) {
                app(UploadFeaturedImageAction::class)
                    ->execute($this->article, $this->featured_image);
            }

            if (!empty($this->gallery_images)) {
                app(UploadGalleryImagesAction::class)
                    ->execute($this->article, $this->gallery_images);
            }

            if ($this->pdf) {
                app(UploadPdfAction::class)
                    ->execute($this->article, $this->pdf);
            }

            // Processar YouTube thumbnail se necessário
            if (session()->pull('use_youtube_thumbnail') && $this->article->youtube_thumbnail) {
                $this->downloadYouTubeThumbnail();
            }

            session()->flash('success', $message);

            return redirect()->route('admin.articles.edit', $this->article);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erro ao salvar: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    protected function validatePublishingRequirements(ContentType $type): array
    {
        $errors = [];
        $requirements = $type->mediaRequirements();

        if (
            $requirements['featured_image'] === 'required' &&
            !$this->featured_image &&
            !session()->has('temp_featured_image')
        ) {
            $errors[] = 'Imagem de destaque é obrigatória para publicação';
        }

        if ($type === ContentType::NEWSPAPER) {
            if (
                $requirements['pdf'] === 'required' &&
                !$this->pdf &&
                !session()->has('temp_pdf')
            ) {
                $errors[] = 'Arquivo PDF da edição é obrigatório para publicação';
            }
            if (!$this->edition) {
                $errors[] = 'Número da edição é obrigatório';
            }
        }

        if ($type === ContentType::VIDEO && !$this->youtube_url) {
            $errors[] = 'URL do YouTube é obrigatória para vídeos';
        }

        if ($type === ContentType::ARTICLE && !$this->content) {
            $errors[] = 'Conteúdo do artigo é obrigatório';
        }

        return $errors;
    }

    protected function processTempMedia(): void
    {
        // if (!$this->article) return;

        // // Imagem de destaque temporária
        // if (session()->has('temp_featured_image')) {
        //     $tempImage = session()->pull('temp_featured_image');
        //     if ($tempImage instanceof TemporaryUploadedFile) {
        //         $action = app(UploadFeaturedImageAction::class);
        //         $action->execute($this->article, $tempImage);
        //     }
        // }

        // // Galeria temporária
        // if (session()->has('temp_gallery_images')) {
        //     $tempImages = session()->pull('temp_gallery_images');
        //     if (is_array($tempImages)) {
        //         $action = app(UploadGalleryImagesAction::class);
        //         $action->execute($this->article, $tempImages);
        //     }
        // }

        // // PDF temporário
        // if (session()->has('temp_pdf')) {
        //     $tempPdf = session()->pull('temp_pdf');
        //     if ($tempPdf instanceof TemporaryUploadedFile) {
        //         $action = app(UploadPdfAction::class);
        //         $action->execute($this->article, $tempPdf);
        //     }
        // }

        $this->loadExistingMedia();
    }

    protected function downloadYouTubeThumbnail(): void
    {
        // Implementar download da thumbnail do YouTube
        // Usar job em background para não travar
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function getContentTypeProperty(): ContentType
    {
        return ContentType::tryFrom($this->type) ?? ContentType::ARTICLE;
    }

    public function getVisibleFieldsProperty(): array
    {
        return $this->content_type->visibleFields();
    }

    public function getPlaceholdersProperty(): array
    {
        return $this->content_type->placeholders();
    }

    public function render()
    {
        $categories = Category::ordered()->get();
        $tags = Tag::orderBy('name')->get();
        $contentTypes = collect(ContentType::cases())->map(fn($type) => [
            'value' => $type->value,
            'label' => $type->label(),
            'icon' => $type->icon(),
            'color' => $type->color(),
        ]);
        $statuses = collect(ContentStatus::cases())->map(fn($status) => [
            'value' => $status->value,
            'label' => $status->label(),
            'color' => $status->color(),
        ]);

        return view('livewire.admin.article-form', [
            'categories' => $categories,
            'tags' => $tags,
            'contentTypes' => $contentTypes,
            'statuses' => $statuses,
            'contentType' => $this->content_type,
            'visibleFields' => $this->visible_fields,
            'placeholders' => $this->placeholders,
        ]);
    }
}
