<?php

namespace App\Services;

use App\DTOs\ExpenseDTO;
use App\Repositories\ExpenseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ExpenseService
{

    /**
     * Create New Record
     */

    public function __construct(protected ExpenseRepository $expenseRepository) {}

    public function getPaginated(?int $perPage = null): LengthAwarePaginator
    {
        return $this->expenseRepository->getPaginated($perPage);
    }

    public function create(ExpenseDTO $expenseDTO): Expense
    {
        return DB::transaction(function () use ($expenseDTO) {
            $data = $this->expenseRepository->create($expenseDTO->toArray());
            return $data;
        });
    }
}
