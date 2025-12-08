<?php

namespace App\Http\Requests\ExpenseType;

use App\Http\Requests\BaseFormRequest;

class StoreExpenseTypeRequest extends BaseFormRequest
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
            'name'        => ['required', 'string', 'max:255','unique:expense_types,name'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Expense type is required.',
            'name.max'          => 'Expense type must not exceed 255 characters.',
            'name.unique'       => 'Expense type is already exists.',
            'is_active.boolean' => 'The active status must be true or false.',
        ];
    }
}
