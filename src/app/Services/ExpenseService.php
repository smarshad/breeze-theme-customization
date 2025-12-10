<?php

namespace App\Services;

use App\DTOs\ExpenseDTO;
use App\Repositories\ExpenseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Models\User;
use DomainException;
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

    public function update(int $id, ExpenseDTO $expenseDTO): Expense
    {

        return DB::transaction(function () use ($id, $expenseDTO) {
            $updateData = $this->expenseRepository->update($id, $expenseDTO->toArray());
            return $updateData;
        });
    }

    public function delete(int $id):bool{

        $delete = $this->expenseRepository->delete($id);

        if(!$delete){
            throw new DomainException('The repository failed to delete the payment method.');
        }
        
        return true;
     }
}
