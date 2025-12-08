<?php

namespace App\DTOs;

class ExpenseDTO
{
    public function __construct(
        public int $category_id,
        public int $expense_type_id,
        public int $payment_method_id,
        public int $expense_date,
        public string $desciption,
        public float $amount,
        public float $cashback,
        public string $notes,
        public string $file_path,
        public readonly ?int $created_by,

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            category_id: $data['category_id'],
            expense_type_id: $data['expense_type_id'],
            payment_method_id: $data['payment_method_id'],
            expense_date: $data['expense_date'],
            desciption: $data['desciption'],
            amount: $data['amount'],
            cashback: $data['cashback'],
            notes: $data['notes'],
            file_path: $data['file_path'],
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
            'desciption' => $this->desciption,
            'amount' => $this->amount,
            'cashback' => $this->cashback,
            'notes' => $this->notes,
            'file_path' => $this->file_path,
            'created_by' => $this->created_by,
        ];
    }
}
