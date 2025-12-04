<?php


namespace App\Services;

use App\DTOs\CategoryDTO;
use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getCategoriesPaginated(int $perPage = 15): LengthAwarePaginator
    {
        // Business logic: e.g., apply global filters based on user context
        return $this->categoryRepository->getPaginated($perPage);
    }

    public function getCategoryById(int $categoryId): Category
    {
        return $this->categoryRepository->findById($categoryId);
    }

    /**
     * Creates a new category within a database transaction.
     */
    public function createCategory(CategoryDTO $categoryDTO): Category
    {
        // Business logic: e.g., check for profanity, trigger external API call
        
        return DB::transaction(function () use ($categoryDTO) {
            // 1. Core persistence
            $category = $this->categoryRepository->create($categoryDTO->toArray());

            // 2. Secondary operation (e.g., logging, creating a related resource)
            // Example: Log the creation event to an audit table
            // AuditLog::create(['user_id' => auth()->id(), 'action' => 'Category Created', 'category_id' => $category->id]);

            return $category;
        });
    }

    /**
     * Updates an existing category within a database transaction.
     */
    public function updateCategory(int $categoryId, CategoryDTO $categoryDTO): Category
    {
        return DB::transaction(function () use ($categoryId, $categoryDTO) {
            // 1. Core persistence
            $updatedCategory = $this->categoryRepository->update($categoryId, $categoryDTO->toArray());

            // 2. Secondary operation (e.g., cache invalidation)
            // Cache::forget('all_categories');

            return $updatedCategory;
        });
    }

    public function deleteCategory(int $categoryId): bool
    {
        // Business logic: e.g., check if category is in use before deleting
        return $this->categoryRepository->delete($categoryId);
    }
}