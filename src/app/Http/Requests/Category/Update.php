<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class Update extends BaseFormRequest
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
        // First, try to get the category object from the route.
        // This works for routes like /categories/{category}
        $category = $this->route('category');

        // If that fails, try to get the ID directly from the route parameters.
        // This works for routes like /categories/{id}
        $categoryId = $category ? $category->id : $this->route('id');
        logAction('iddddd', 'info', [$categoryId]);
        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($categoryId)],
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
