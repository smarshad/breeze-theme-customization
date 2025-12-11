<?php

namespace App\Interfaces;

use App\DTOs\MenuDTO;
use App\Models\Menu;
use Illuminate\Pagination\Paginator;

interface MenuRepositoryInterface
{
    /**
     * Get all menus with pagination.
     */
    public function paginate(int $perPage = 15): Paginator;
    /**
     * Get all menus.
     */
    public function getAll();
    /**
     * Get menu by ID.
     */
    public function getById(int $id): ?Menu;
    /**
     * Create a new menu.
     */
    public function create(MenuDTO $dto): Menu;
    /**
     * Update an existing menu.
     */
    public function update(int $id, MenuDTO $dto): Menu;
    /**
     * Delete a menu.
     */
    public function delete(int $id): bool;
    /*** Get active menus only.
     */
    public function getActive();
    /**
     * Get root menus with their children.
     */
    public function getRootWithChildren();
    /**
     * Get menu children.
     */
    public function getChildren(int $parentId);
    /**
     * Check if menu exists.
     */
    public function exists(int $id): bool;
}
