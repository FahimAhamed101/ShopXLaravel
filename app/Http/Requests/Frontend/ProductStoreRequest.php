<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric'],
            'special_price' => ['nullable', 'numeric'],
            'quantity' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string'],
            'categories' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
        ];
    }
}
