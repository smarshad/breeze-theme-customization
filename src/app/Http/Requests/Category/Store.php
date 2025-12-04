<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseFormRequest;

class Store extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255','unique:categories,name'],
            'description' => ['nullable', 'string'],
            'color_code'  => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'], // new field
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max'      => 'Category name must not exceed 255 characters.',
            'is_active.boolean' => 'The active status must be true or false.',
        ];
    }
}
