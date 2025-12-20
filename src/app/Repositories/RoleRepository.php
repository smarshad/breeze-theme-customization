<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{

    public function __construct(protected Role $model) {}

    public function getPaginated(?int $perPage = null, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with('permissions')->withCount('permissions')->paginate(
            $perPage ?? config('pagination.default'),
            $columns
        );
    }

    /**
     * Create a new Role.
     */
    public function createRole(array $data):Role
    {
        logAction('RoleRepository','info',$data);
        return $this->model->create($data);
    }
}
