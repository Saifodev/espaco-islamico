<?php

namespace App\Domains\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // A autorização é feita nos controllers
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', Rule::in(['article', 'video', 'newspaper', 'news'])],
            'title' => ['required', 'min:3', 'max:255'],
            'slug' => [
                'nullable',
                'max:255',
                Rule::unique('articles')->ignore($this->route('article')),
            ],
            'excerpt' => ['nullable', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],

            // Regras específicas por tipo
            'content' => ['required_if:type,article,news', 'nullable', 'min:10'],
            'youtube_url' => [
                'required_if:type,video',
                'nullable',
                'url',
                function ($attribute, $value, $fail) {
                    if ($value && !str_contains($value, 'youtube.com') && !str_contains($value, 'youtu.be')) {
                        $fail('A URL deve ser do YouTube.');
                    }
                }
            ],
            'edition' => ['required_if:type,newspaper', 'nullable', 'max:100'],
            'is_sellable' => ['nullable', 'boolean'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],

            // Imagem
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],

            // PDF
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'remove_pdf' => ['nullable', 'boolean'],

            // SEO
            'seo_title' => ['nullable', 'max:70'],
            'seo_description' => ['nullable', 'max:160'],
            'seo_keywords' => ['nullable', 'max:255'],
        ];

        // Se slug não foi fornecido, não validamos (será gerado)
        if (!$this->filled('slug')) {
            unset($rules['slug']);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'featured_image.image' => 'O arquivo deve ser uma imagem válida (JPG, PNG, WebP)',
            'featured_image.max' => 'A imagem não pode ter mais que 5MB',
            'featured_image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, webp',
            'youtube_url.required_if' => 'A URL do YouTube é obrigatória para vídeos',
            'edition.required_if' => 'O número da edição é obrigatório para jornais',
            'content.required_if' => 'O conteúdo é obrigatório para artigos e notícias',
            'pdf_file.mimes' => 'O arquivo deve ser um PDF válido',
            'pdf_file.max' => 'O PDF não pode ter mais que 20MB',
        ];
    }

    protected function prepareForValidation()
    {
        // Gerar slug se não fornecido
        if (!$this->has('slug') || empty($this->slug)) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title),
            ]);
        }

        if ($this->has('is_sellable')) {
            $this->merge([
                'is_sellable' => $this->boolean('is_sellable'),
            ]);
        }
    }
}
