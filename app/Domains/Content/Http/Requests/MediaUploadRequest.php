<?php

namespace App\Domains\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domains\Media\Enums\MediaCollectionType;

class MediaUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('update', $this->route('article'));
    }

    public function rules(): array
    {
        $collection = MediaCollectionType::tryFrom($this->input('collection', ''));
        
        if (!$collection) {
            return [
                'collection' => ['required', 'in:' . implode(',', array_column(MediaCollectionType::cases(), 'value'))],
            ];
        }

        $rules = [
            'collection' => ['required', 'in:' . implode(',', array_column(MediaCollectionType::cases(), 'value'))],
        ];

        // Adiciona regras baseadas na coleção
        if ($collection->allowsMultiple()) {
            $rules['files'] = ['required', 'array', 'min:1', 'max:10'];
            $rules['files.*'] = $collection->validationRules();
        } else {
            $rules['file'] = ['required'] + $collection->validationRules();
        }

        return $rules;
    }

    public function messages(): array
    {
        $collection = MediaCollectionType::tryFrom($this->input('collection', ''));
        
        if (!$collection) {
            return [
                'collection.required' => 'A coleção é obrigatória',
                'collection.in' => 'Coleção inválida',
            ];
        }

        if ($collection->allowsMultiple()) {
            return [
                'files.required' => 'Selecione pelo menos um arquivo',
                'files.max' => 'Máximo de 10 arquivos por upload',
                'files.*.mimes' => $collection->validationMessages()['mimes'] ?? 'Tipo de arquivo inválido',
                'files.*.max' => $collection->validationMessages()['max'] ?? 'Arquivo muito grande',
            ];
        }

        return $collection->validationMessages();
    }

    protected function prepareForValidation(): void
    {
        // Garante que collection seja string válida
        if ($this->has('collection')) {
            $this->merge([
                'collection' => $this->collection,
            ]);
        }
    }
}