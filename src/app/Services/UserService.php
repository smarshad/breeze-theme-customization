<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\Permission;
use App\Models\User;
use App\Models\Role;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    /**
     * Get all users
     */
    public function getUsers(array $filters = []): Paginator
    {
        $query = User::query()->with('role');

        if (isset($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('email', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('name')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new user
     */
    public function create(UserDTO $dto): User
    {

        $data = $dto->toArray();
        if (!isset($data['created_by']) && Auth::check()) {
            $data['created_by'] = Auth::id();
        }

        // Create the user
        $user = User::create($data);

        // Assign roles using Spatie
        $this->assignRolesToUser($user, $dto->getRoles());

        // Assign direct permissions
        if (!empty($dto->getPermissions())) {
            $user->givePermissionTo($dto->getPermissions());
        }

        return $user->load(['roles', 'permissions', 'creator']);
    }

    public function update(User $user, UserDTO $dto): User
    {
        // Update user attributes
        $user->update($dto->toArray());

        // Sync roles if provided in DTO
        if ($dto->getRoles()) {
            $this->syncRolesForUser($user, $dto->getRoles());
        }

        // Sync permissions if provided
        if ($dto->getPermissions() !== NULL) {
            $user->syncPermissions($dto->getPermissions());
        }

        return $user->load('roles');
    }

    /**
     * Delete a user (soft delete)
     */
    public function deleteById(int $id): bool
    {
        $user = User::find($id);

        if (! $user) {
            throw new ModelNotFoundException("User not found.");
        }

        return $this->delete($user);
    }

    protected function assignRolesToUser(User $user, array $roles): void
    {
        if (empty($roles)) {
            return;
        }

        // Check if roles are IDs or names
        $firstRole = $roles[0] ?? NULL;

        if (is_numeric($firstRole)) {
            // Roles are IDs, get role models
            $roleModels = Role::whereIn('id', $roles)->get();
            $user->assignRole($roleModels);
        } else {
            // Roles are names
            $user->assignRole($roles);
        }
    }

    protected function syncRolesForUser(User $user, array $roles): void
    {
        if (empty($roles)) {
            $user->roles()->detach();
            return;
        }

        // Check if roles are IDs or names
        $firstRole = $roles[0] ?? NULL;

        if (is_numeric($firstRole)) {
            // Treat as IDs (string or int)
            $roleModels = Role::whereIn('id', $roles)->get();
        } else {
            // Treat as names
            $roleModels = Role::whereIn('name', $roles)->get();
        }

        $user->syncRoles($roleModels);
    }

    /**
     * Delete a user (soft delete)
     */
    public function delete(User $user): bool
    {
        if ($user->hasRole('Super Admin')) {
            // you can throw an exception or just return false
            throw new \RuntimeException('Super Admin user cannot be deleted.');
        }
        $dto = UserDTO::fromModel($user);
        // Manually clear roles/permissions before soft delete
        $user->syncRoles([]);
        $user->syncPermissions([]); // optional if you use direct perms
        logAction('user deleted:', 'info', ['user' => $dto->toArray(), 'role' => $user->hasRole('Super Admin')]);
        return $user->delete();
    }

    public function assignPermissions(User $user, array $selectedPermissions): User
    {
        logAction('assignPermissions user:', 'info', ['user' => $user->toArray()]);
        $firstPermission = $selectedPermissions[0] ?? NULL;

        if (is_numeric($firstPermission)) {
            // Treat as IDs (string or int)
            $permissionModels = Permission::whereIn('id', $selectedPermissions)->get();
        } else {
            // Treat as names
            $permissionModels = Permission::whereIn('name', $selectedPermissions)->get();
        }
        $user->syncPermissions($permissionModels);

        return $user->load(['roles', 'permissions']);
    }
}
