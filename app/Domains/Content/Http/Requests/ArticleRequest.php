<?php
// app/Domains/Content/Http/Requests/ArticleRequest.php

namespace App\Domains\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Domains\Content\Enums\ContentStatus;
use App\Domains\Content\Enums\ContentType;
use App\Domains\Content\Models\Article;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('POST')) {
            return $this->user()->can('create', Article::class);
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return $this->user()->can('update', $this->route('article'));
        }

        return false;
    }

    public function rules(): array
    {
        $articleId = $this->route('article')?->id;
        $type = $this->input('type', 'article');
        $status = $this->input('status', 'draft');
        
        $contentType = ContentType::tryFrom($type) ?? ContentType::ARTICLE;
        $isPublishing = $status === ContentStatus::PUBLISHED->value;
        
        // Regras base
        $rules = [
            'type' => ['required', Rule::enum(ContentType::class)],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('articles')->ignore($articleId),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => [
                'nullable',
                'date',
                'after_or_equal:' . now()->subDay()->format('Y-m-d H:i:s')
            ],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
        
        // Adicionar regras específicas do tipo
        $typeRules = $contentType->validationRules($isPublishing);
        $rules = array_merge($rules, $typeRules);
        
        // Regras de mídia baseadas no tipo
        $mediaRequirements = $contentType->mediaRequirements();
        
        if ($isPublishing) {
            if ($mediaRequirements['featured_image'] === 'required') {
                $rules['featured_image_required'] = ['required', 'boolean'];
            }
            
            if ($contentType === ContentType::NEWSPAPER) {
                $rules['pdf_required'] = ['required', 'boolean'];
            }
        }
        
        return $rules;
    }

    public function messages(): array
    {
        $type = $this->input('type', 'article');
        $contentType = ContentType::tryFrom($type) ?? ContentType::ARTICLE;
        
        $messages = [
            'title.required' => 'O título é obrigatório',
            'type.required' => 'O tipo de conteúdo é obrigatório',
            'status.required' => 'O status é obrigatório',
            'published_at.after_or_equal' => 'A data de publicação não pode ser muito antiga',
            'seo_title.max' => 'O título SEO deve ter no máximo 70 caracteres',
            'seo_description.max' => 'A descrição SEO deve ter no máximo 160 caracteres',
        ];
        
        // Mensagens específicas por tipo
        if ($contentType === ContentType::VIDEO) {
            $messages['youtube_url.required'] = 'A URL do YouTube é obrigatória para vídeos publicados';
            $messages['youtube_url.regex'] = 'Por favor, insira uma URL válida do YouTube';
        }
        
        if ($contentType === ContentType::NEWSPAPER) {
            $messages['edition.required'] = 'O número da edição é obrigatório para jornais publicados';
        }
        
        if ($contentType === ContentType::ARTICLE) {
            $messages['content.required'] = 'O conteúdo é obrigatório para artigos publicados';
        }
        
        return $messages;
    }

    protected function prepareForValidation(): void
    {
        // Gerar slug se não fornecido
        if (!$this->has('slug') || empty($this->slug)) {
            $this->merge([
                'slug' => str($this->title)->slug()
            ]);
        }
        
        // Definir published_at se publicado e não definido
        if ($this->status === ContentStatus::PUBLISHED->value && !$this->published_at) {
            $this->merge([
                'published_at' => now()
            ]);
        }
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            $status = $this->input('status');
            
            // Validações adicionais para publicação
            if ($status === ContentStatus::PUBLISHED->value) {
                $this->validatePublishing($validator);
            }
        });
    }

    protected function validatePublishing($validator)
    {
        $type = ContentType::tryFrom($this->input('type')) ?? ContentType::ARTICLE;
        $requirements = $type->mediaRequirements();
        
        // Verificar imagem de destaque se required
        if ($requirements['featured_image'] === 'required') {
            $article = $this->route('article');
            
            if ($article) {
                $hasImage = $article->hasFeaturedImage();
            } else {
                $hasImage = $this->has('featured_image_uploaded') || session()->has('temp_featured_image');
            }
            
            if (!$hasImage) {
                $validator->errors()->add(
                    'featured_image', 
                    'Artigos publicados precisam de uma imagem de destaque'
                );
            }
        }
        
        // Verificar PDF para jornal
        if ($type === ContentType::NEWSPAPER && $requirements['pdf'] === 'required') {
            $article = $this->route('article');
            
            if ($article) {
                $hasPdf = $article->hasMedia('pdf');
            } else {
                $hasPdf = $this->has('pdf_uploaded');
            }
            
            if (!$hasPdf) {
                $validator->errors()->add(
                    'pdf', 
                    'Jornais publicados precisam de um arquivo PDF da edição'
                );
            }
        }
    }
}