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

    public function getPaginated(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        // Advanced: We can add complex filtering/sorting logic here
        return Category::query()->paginate($perPage, $columns);
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