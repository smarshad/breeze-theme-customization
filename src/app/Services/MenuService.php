<?php

namespace App\Services;

use App\DTOs\MenuDTO;
use App\Interfaces\MenuRepositoryInterface;
use App\Models\Menu;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MenuService
{
    public function __construct(
        protected MenuRepositoryInterface $menuRepository
    ) {}


    public function getPaginated(?int $perPage = null): LengthAwarePaginator
    {
        return $this->menuRepository->getPaginated($perPage);
    }

    public function createMenu(MenuDTO $menuDTO): Menu
    {
        $data = $menuDTO->toArray();

        // If order is not set, calculate the next order value
        if (!isset($data['order'])) {
            $data['order'] = $this->menuRepository->getMaxOrder($data['parent_id']) + 1;
        }

        return $this->menuRepository->createMenu($data);
    }

    /**
     * Get all menus for the parent dropdown.
     *
     * @return Collection
     */
    public function getMenus(): Collection
    {
        return Menu::all(['id', 'name', 'parent_id']);
    }

    public function flattenMenu($data): array // Changed return type from Collection to array
    {
        // 1. Check if $data is a Collection and convert it to a native array.
        // This is the fix for the error on line 47.
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }
        
        // 2. Pass the now-native array to the Repository.
        return $this->menuRepository->flattenMenu($data);
    }

    /**
     * Get all permissions for the form dropdown (assuming Spatie's Permission model).
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        // We need to assume the Permission model exists and is accessible
        // In a real application, you would inject or resolve this dependency
        try {
            return \Spatie\Permission\Models\Permission::all(['id', 'name', 'module']);
        } catch (\Throwable $e) {
            // Fallback for demonstration if Spatie is not installed
            return collect([]);
        }
    }
}
