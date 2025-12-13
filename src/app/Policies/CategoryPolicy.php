<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('category.view.any');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->can('category.view.any')
            || (
                $user->can('category.view.own')
                && $category->created_by === $user->id
            );
    }

    public function create(User $user): bool
    {
        return $user->can('category.create');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can('category.update.any')
            || (
                $user->can('category.update.own')
                && $category->created_by === $user->id
            );
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can('category.delete.any')
            || (
                $user->can('category.delete.own')
                && $category->created_by === $user->id
            );
    }
}
