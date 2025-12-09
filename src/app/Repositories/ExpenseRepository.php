<?php 

namespace App\Repositories;

use App\Interfaces\ExpenseRepositoryInterface;
use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class ExpenseRepository implements ExpenseRepositoryInterface {

    public function __construct(protected Expense $model){}

    public function create (array $data):Expense{
        return $this->model->create($data);
    }

    public function getPaginated(?int $perPage = null, array $columns = ['*']): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.default');
        return $this->model->with('creator','category','paymentMethod','expenseType')->paginate($perPage, $columns);
    }
}
?>