<?php

namespace App\Repositories;

use App\Interfaces\MenuRepositoryInterface;
use App\Models\Menu;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MenuRepository implements MenuRepositoryInterface
{
    public function __construct(protected Menu $model) {}

    /**
     * Create a new menu.
     *
     * @param array $menuDetails
     * @return Menu
     */


    public function createMenu(array $menuDetails): Menu
    {
        return $this->model->create($menuDetails);
    }

    public function getMaxOrder(?int $parentId = null): int
    {
        return Menu::where('parent_id', $parentId)->max('order') ?? 0;
    }

    public function getPaginated(?int $perPage = null, array $columns = ['*']): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.default');
        return $this->model->with('parent', 'children', 'permission', 'creator')->paginate($perPage, $columns);
    }
    
    
    /**
     * Public method matching the interface.
     * It handles the full process: flat data -> nested tree -> flat display array.
     *
     * @param array $data The flat array of menu items (with 'parent_id').
     * @return array The flattened menu list with display titles.
     */
    public function flattenMenu(array $data): array
    {
        // 1. Convert the flat array (with parent_id) into a nested array (with children).
        $nestedTree = $this->buildTree($data);

        // 2. Flatten the nested array into the final display array.
        // Start with an empty parent path and level 0.
        return $this->recursiveFlattenMenu($nestedTree);
    }

    /**
     * Converts a flat array of items with a 'parent_id' into a nested, hierarchical array.
     * Uses an efficient iterative approach. (Unchanged)
     */
    private function buildTree(array $elements, $parentId = null): array
    {
        $nested = [];
        $lookup = [];

        // Index all elements by their ID
        foreach ($elements as &$element) {
            $element['children'] = []; // Initialize children array
            $lookup[$element['id']] = &$element;
        }
        unset($element); // Unset reference

        // Build the tree
        foreach ($elements as $element) {
            $currentParentId = $element['parent_id'] ?? null;
            if ($currentParentId === 0 || $currentParentId === '') {
                $currentParentId = null;
            }

            if ($currentParentId !== null && isset($lookup[$currentParentId])) {
                // Add to parent's children array
                $lookup[$currentParentId]['children'][] = &$lookup[$element['id']];
            } else {
                // Add to the root level
                $nested[] = &$lookup[$element['id']];
            }
        }

        return $nested;
    }

    /**
     * Private helper method for the recursive logic.
     * MODIFIED to build the full path string AND prepend the indentation marker.
     *
     * @param array $items The current level of menu items.
     * @param string $parentPath The path string of the parent item (e.g., "Masters").
     * @param string $separator The visual separator (e.g., '-->').
     * @param int $level The current depth level.
     * @return array The flattened list of menu items.
     */
    private function recursiveFlattenMenu(array $items, string $parentPath = '', string $separator = '-->', int $level = 0): array
    {
        $flatList = [];

        foreach ($items as $item) {
            $currentTitle = $item['name'];
            
            // 1. Build the full path for the current item
            if (!empty($parentPath)) {
                // If there is a parent path, append the current title to it
                $fullPath = $parentPath . ' ' . $separator . ' ' . $currentTitle;
            } else {
                // Root level item
                $fullPath = $currentTitle;
            }

            // 2. Apply the indentation marker based on level
            $indentation = '';
            if ($level > 0) {
                // Repeat the separator and a space for each level
                // This prepends the indentation to the full path.
                $indentation = str_repeat($separator . ' ', $level);
            }
            
            // 3. Combine indentation and full path
            $displayTitle = $indentation . $fullPath;

            // 4. Add the current item to the flat list
            $flatList[] = [
                'id' => $item['id'],
                'title' => $item['name'],
                'display_title' => $displayTitle,
                'level' => $level,
            ];

            // 5. Recursively flatten children
            if (!empty($item['children'])) {
                // Calculate the new parent path for the children
                $newParentPath = $fullPath; // The full path of the current item becomes the parent path for the next level

                $flatList = array_merge(
                    $flatList, 
                    $this->recursiveFlattenMenu($item['children'], $newParentPath, $separator, $level + 1)
                );
            }
        }

        return $flatList;
    }
}
