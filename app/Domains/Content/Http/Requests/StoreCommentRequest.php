<?php

namespace App\Domains\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Todos podem comentar, mas passam por aprovação
    }

    public function rules(): array
    {
        return [
            'article_id' => ['required', 'exists:articles,id'],
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'content' => ['required', 'string', 'min:3', 'max:2000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 2 caracteres.',
            'content.required' => 'O comentário é obrigatório.',
            'content.min' => 'O comentário deve ter pelo menos 3 caracteres.',
            'content.max' => 'O comentário não pode ter mais de 2000 caracteres.',
        ];
    }
}