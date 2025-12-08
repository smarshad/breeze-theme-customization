<?php

namespace App\Services;

use App\DTOs\ExpenseTypeDTO;
use App\Interfaces\ExpenseTypeRepositoryInterface;
use App\Models\ExpenseType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\CannotDeleteExpenseTypeException;
use DomainException;

class ExpenseTypeService
{
    // Define a constant for the cache key
    protected const CACHE_KEY_ALL = 'expense_types.all';

    public function __construct(protected ExpenseTypeRepositoryInterface $expenseTypeRepository) {}

    /**
     * Retrieves paginated expense types.
     */
    public function getExpenseTypePaginated(int $perPage = 15): LengthAwarePaginator {
        // Caching is typically not used for paginated results unless the query is very expensive and static.
        return $this->expenseTypeRepository->getPaginated($perPage);
    }

    /**
     * Retrieves a single expense type by ID.
     */
    public function getExpenseTypeById(int $id): ExpenseType
    {
        // Use the repository's findById method, which should handle the "not found" case (e.g., throwing ModelNotFoundException)
        return $this->expenseTypeRepository->findById($id);
    }

    /**
     * Creates a new expense type.
     */
    public function create(ExpenseTypeDTO $expenseTypeDTO): ExpenseType
    {
        return DB::transaction(function () use ($expenseTypeDTO) {
            $expenseType = $this->expenseTypeRepository->create($expenseTypeDTO->toArray());
            
            // Invalidate the cache for the 'all' list
            Cache::forget(self::CACHE_KEY_ALL);
            
            return $expenseType;
        });
    }

    /**
     * Updates an existing expense type.
     */
    public function update(int $id, ExpenseTypeDTO $expenseTypeDTO): ExpenseType
    {
        return DB::transaction(function () use ($id, $expenseTypeDTO) {
            // 1. Core persistence
            $updatedExpenseType = $this->expenseTypeRepository->update($id, $expenseTypeDTO->toArray());

            // 2. Invalidate the cache for the 'all' list
            Cache::forget(self::CACHE_KEY_ALL);

            return $updatedExpenseType;
        });
    }

    /**
     * Deletes an expense type after validation.
     * @throws CannotDeleteExpenseTypeException
     */
    public function delete(int $id): bool
    {
        // Business logic validation
        $this->validateExpenseTypeCanBeDeleted($id);
        
        // Perform deletion
        $deleted = $this->expenseTypeRepository->delete($id);
        
        if (!$deleted) {
            // Throw a more specific exception if the repository failed to delete for an unknown reason
            throw new DomainException('The repository failed to delete the expense type.');
        }
        
        // Invalidate the cache for the 'all' list
        Cache::forget(self::CACHE_KEY_ALL);
        
        return true;
    }

     /**
     * Business rule: Check if the expense type can be deleted.
     * @throws DomainException
     */
    private function validateExpenseTypeCanBeDeleted(int $id): void
    {
        if ($this->expenseTypeRepository->hasRelatedExpenses($id)) {
            // Use DomainException with a specific message for the controller to handle
            throw new DomainException('Cannot delete expense type. Related expense records exist.');
        }
    }
}