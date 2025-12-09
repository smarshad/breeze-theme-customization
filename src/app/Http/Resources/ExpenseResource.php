<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'amount' => $this->amount,
            'cashback' => $this->cashback,
            'notes' => $this->notes,
            'file_path' => $this->file_path,
            'category_id' => $this->category_id,
            'expense_type_id' => $this->expense_type_id,
            'payment_method_id' => $this->payment_method_id,
            'expense_date' => $this->expense_date,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_by' => $this->created_by,
            // full creator info (only if relation is loaded)
            'creator'    => $this->whenLoaded('creator', function () {
                return [
                    // 'id'    => $this->creator->id,
                    'name'  => $this->creator->name,
                    // 'email' => $this->creator->email,
                ];
            }),
            'category'    => $this->whenLoaded('category', function () {
                return [
                    // 'id'    => $this->category->id,
                    'name'  => $this->category->name,
                ];
            }),
            'expenseType'    => $this->whenLoaded('expenseType', function () {
                return [
                    // 'id'    => $this->expenseType->id,
                    'name'  => $this->expenseType->name,
                ];
            }),
            'paymentMethod'    => $this->whenLoaded('paymentMethod', function () {
                return [
                    // 'id'    => $this->paymentMethod->id,
                    'name'  => $this->paymentMethod->name,
                ];
            }),
        ];
    }
}
