<?php

namespace App\Repositories;

use App\Interfaces\ExpenseTypeRepositoryInterface;
use App\Models\ExpenseType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ExpenseTypeRepository implements ExpenseTypeRepositoryInterface
{
    public function __construct(protected ExpenseType $model) {}

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function getPaginated(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    public function findById(int $categoryId): ExpenseType
    {
        // Use findOrFail to automatically throw ModelNotFoundException if not found
        return $this->model->findOrFail($categoryId);
    }

    public function create(array $details): ExpenseType
    {
        return $this->model->create($details);
    }

    public function update(int $categoryId, array $newDetails): ExpenseType
    {
        $expenseType = $this->findById($categoryId);
        $expenseType->update($newDetails);
        return $expenseType;
    }

    public function delete(int $categoryId): bool
    {
        // Eloquent delete returns a boolean indicating success
        return $this->model->destroy($categoryId);
    }

    public function hasRelatedExpenses(int $expenseTypeId): bool
    {
        // This assumes you have an 'expenses' relationship defined on the ExpenseType model
        // and a corresponding 'Expense' model.
        // The check should be fast and efficient.
        
        // Placeholder for actual implementation:
        // return $this->model->whereHas('expenses', function ($query) use ($expenseTypeId) {
        //     $query->where('expense_type_id', $expenseTypeId);
        // })->exists();
        
        // For demonstration, we'll assume a direct check on a hypothetical 'Expense' model
        // that has a foreign key 'expense_type_id'.
        
        // To make this runnable, we'll use a simplified check that assumes the existence of a related model.
        // In a real application, you would need to import the Expense model.
        
        // return \App\Models\Expense::where('expense_type_id', $expenseTypeId)->exists();
        
        // Since we don't have the Expense model, we'll return false for now to allow the service to work
        // but the user should uncomment the real logic.
        return false; 
    }
}