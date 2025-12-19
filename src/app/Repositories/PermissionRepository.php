<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\PermissionRepositoryInterface;
use App\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{

    public function __construct(protected Permission $model) {}

    public function getPaginated(?int $perPage = null, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate(
            $perPage ?? config('pagination.default'),
            $columns
        );
    }
}
