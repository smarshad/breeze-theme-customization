<?php

namespace App\DTOs;

class ExpenseDTO
{
    public function __construct(
        public int $category_id,
        public int $expense_type_id,
        public int $payment_method_id,
        public string $expense_date,
        public string $description,
        public float $amount,
        public float $cashback,
        public ?string $notes = NULL,
        public ?string $bank_account = NULL,
        public ?string $file_path = NULL,
        public readonly ?int $created_by = NULL,

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            category_id: $data['category_id'],
            expense_type_id: $data['expense_type_id'],
            payment_method_id: $data['payment_method_id'],
            expense_date: $data['expense_date'],
            description: $data['description'],
            amount: $data['amount'],
            cashback: $data['cashback'] ?? 0.0,
            notes: $data['notes'] ?? NULL,
            bank_account: $data['bank_account'] ?? NULL,
            file_path: isset($data['file_path']) ? (string) $data['file_path'] : NULL,
            created_by: $data['created_by'],
        );
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'expense_type_id' => $this->expense_type_id,
            'payment_method_id' => $this->payment_method_id,
            'expense_date' => $this->expense_date,
            'description' => $this->description,
            'amount' => $this->amount,
            'cashback' => $this->cashback,
            'notes' => $this->notes,
            'bank_account' => $this->bank_account,
            'file_path' => $this->file_path,
            'created_by' => $this->created_by,
        ];
    }
}
