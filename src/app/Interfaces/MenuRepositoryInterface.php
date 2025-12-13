<?php

namespace App\Interfaces;

use App\Models\Menu;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MenuRepositoryInterface
{
    /**
     * Get all menus, optionally filtered by parent_id.
     *
     * @param int|null $parentId
     * @return Collection
     */
    // public function getAllMenus(?int $parentId = null): Collection;

    /**
     * Get a menu by its ID.
     *
     * @param int $menuId
     * @return Menu|null
     */
    // public function getMenuById(int $menuId): ?Menu;

    /**
     * Create a new menu.
     *
     * @param array $menuDetails
     * @return Menu
     */
    public function createMenu(array $menuDetails): Menu;

    /**
     * Update an existing menu.
     *
     * @param int $menuId
     * @param array $newDetails
     * @return Menu|null
     */
    // public function updateMenu(int $menuId, array $newDetails): ?Menu;

    /**
     * Delete a menu by its ID.
     *
     * @param int $menuId
     * @return bool
     */
    // public function deleteMenu(int $menuId): bool;

    /**
     * Get the maximum order value for a given parent.
     *
     * @param int|null $parentId
     * @return int
     */
    public function getMaxOrder(?int $parentId = null): int;

    /**
     * Flattens a hierarchical menu array into a single-level array
     * suitable for an HTML select dropdown.
     *
     * @param array $data The hierarchical menu data.
     * @return array The flattened menu list.
     */
    public function flattenMenu(array $data): array;

    public function getPaginated(?int $perPage = null, array $column = ['*']): LengthAwarePaginator;

    /**
     * Update an existing Record
     */

    public function update(Menu $menu, array $data): Menu;

    /**
     * Check if there are any related submenu records for a given  ID.
     */
    public function hasRelatedChild(int $id): bool;
    
}
