<?php

namespace App\Repositories;

use App\Interfaces\ExpenseRepositoryInterface;
use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ExpenseRepository implements ExpenseRepositoryInterface
{

    public function __construct(protected Expense $model) {}

    public function getAll(): Collection{
        return $this->model->all();
    }

    public function create(array $data): Expense
    {
        return $this->model->create($data);
    }

    public function getPaginated(int $perPage = 15, array $columns = ['*'], ?int $userId = null, ?string $search = null): LengthAwarePaginator {
        $query = $this->model
            ->with(['creator', 'category', 'paymentMethod', 'expenseType']);
    
        if ($userId !== null) {
            $query->where('created_by', $userId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('paymentMethod', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('expenseType', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sql = vsprintf(
            str_replace('?', '%s', $query->toSql()),
            collect($query->getBindings())->map(fn($b) => "'$b'")->toArray()
        );

        \Log::info($sql);
        return $query->paginate($perPage, $columns);
    }
    
    public function update(int $id, array $data): Expense
    {
        $expense = $this->findById($id);

        if (array_key_exists('file_path', $data)) {
            $newPath = $data['file_path'];
            $oldPath = $expense->file_path;
    
            if ($oldPath && $newPath && $oldPath !== $newPath) {
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }
        $expense->update($data);
        return $expense;
    }

    private function findById(int $id): Expense
    {
        // Use findOrFail to automatically throw ModelNotFoundException if not found
        return $this->model->findOrFail($id);
    }

    public function delete(int $id): bool{
        return $this->model->destroy($id);
    }

}
