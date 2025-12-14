<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(): Collection
    {
        return Category::all();
    }

    public function getPaginated(int $perPage = 15, array $columns = ['*'], ?int $userId = null): LengthAwarePaginator
    {
        // Advanced: We can add complex filtering/sorting logic here
        /*
         * $query = Category::query();

            $sql = vsprintf(
                str_replace('?', '%s', $query->toSql()),
                collect($query->getBindings())->map(fn($b) => "'$b'")->toArray()
            );

            \Log::info($sql);
         */
        // return Category::query()->paginate($perPage, $columns);

        $query = Category::query();

        if ($userId !== null) {
            // Filter categories by the user who created them
            $query->where('created_by', $userId);
        }

        // Advanced: We can add complex filtering/sorting logic here
        return $query->paginate($perPage, $columns);
    }

    public function findById(int $categoryId): ?Category
    {
        // Using findOrFail ensures the Service layer doesn't need to check for null
        return Category::findOrFail($categoryId);
    }

    public function create(array $details): Category
    {
        return Category::create($details);
    }

    public function update(int $categoryId, array $newDetails): Category
    {
        $category = $this->findById($categoryId);
        $category->update($newDetails);
        return $category;
    }

    public function delete(int $categoryId): bool
    {
        return $this->findById($categoryId)->delete();
    }
}
