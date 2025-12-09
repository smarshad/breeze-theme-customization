<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'category_id'         => ['required', 'integer', 'exists:categories,id'],
            'expense_type_id'     => ['required', 'integer', 'exists:expense_types,id'],
            'payment_method_id'   => ['required', 'integer', 'exists:payment_methods,id'],
            'expense_date'        => ['required', 'date'],
            'amount'              => ['required', 'numeric', 'min:1', 'max:1000000'],
            'cashback'            => ['nullable', 'numeric'],
            'description'         => ['required', 'string', 'max:500'],
            'notes'               => ['nullable', 'string', 'max:255'],
            // Optional upload
            'uploaded_file'       => ['nullable', 'file', 'mimes:pdf,jpg,png,jpeg', 'max:10240'],
            // Optional string path (may remain null)
            'file_path'           => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category does not exist',
            'expense_type_id.required' => 'Expense type is required',
            'payment_method_id.required' => 'Payment method is required',
            'expense_date.required' => 'Expense date is required',
            'expense_date.date' => 'Expense date must be a valid date',
            'amount.required' => 'Expense amount is required',
            'amount.numeric' => 'Expense amount must be a number',
            'cashback.numeric' => 'Cashback amount must be a number',
            'description.required' => 'Expense description is required',
            'uploaded_file.required_without' => 'Please upload a file or provide an existing file path.',
            'file_path.required_without'     => 'Please upload a file or provide a file path.',
        ];
    }
}
