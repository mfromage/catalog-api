<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'main_image_id' => 'nullable|exists:images,id',
            'attributes' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'exists:images,id',
        ];
    }
}
