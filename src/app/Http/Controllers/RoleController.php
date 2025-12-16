<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('id', 'desc')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissionsGrouped = Permission::getGroupedByModule();
        return view('admin.roles.create', compact('permissionsGrouped'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        try {

            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            logAction('New Role  Created', 'info', [
                'role_id'   => $role->id,
                'role_name' => $role->name,
            ]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                logAction('Assign Permission To Role', 'info', ['permission_names' => $permissions->pluck('name')->toArray()]);
                $role->syncPermissions($permissions);
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('roles.index'),
                    'message' => 'Role created successfully.',
                ], 201);
            }

            return redirect()->route('roles.index')->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            logAction('Eror While Creating New Role', 'error', [
                'role_name' => $role->name,
            ]);
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating role: ' . $e->getMessage(),
                ], 500); // ✅ 500 for server errors
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = Role::find($id);
        if (!$role) {
            logAction('Role not found for editing', 'warning', [
                'role_id' => $id,
            ]);

            return redirect()
                ->route('roles.index')
                ->with('error', __('roles.not_found'));
        }

        $permissionsGrouped = Permission::getGroupedByModule();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissionsGrouped', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        
        $validated = $request->validated();
        try {

            $role = role::find($role->id);

            if (!$role) {
                logAction('Role not found for update', 'warning', [
                    'role_id' => $role->id,
                ]);

                return response()->json(['message' => 'Updated successfully']);
            }
            // Save old values
            $old = $role->only(['name', 'description']);

            DB::beginTransaction();

            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            logAction('Role Updated', 'info', [
                'permission_id'      => $role->id,
                'old_name'           => $old['name'],
                'new_name'           => $role->name,
                'old_description'    => $old['description'],
                'new_description'    => $role->description,
            ]);

            // Sync permissions for the role
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
                logAction('Role Updated update permission', 'info', ['permission_names' => $permissions->pluck('name')->toArray()]);
            }

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('roles.edit', $role->id),
                    'message' => 'Role updated successfully.',
                ], 200);
            }

            return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            logAction('Error On Updating Role', 'warning', [
                'permission_id'      => $role->id,
                'old_name'           => $old['name'],
                'new_name'           => $role->name,
                'old_description'    => $old['description'],
                'new_description'    => $role->description,
                'error'              => $e->getMessage(),
            ]);
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating role: ' . $e->getMessage(),
                ], 500); // ✅ 500 for server errors
            }
            return redirect()->back()->withInput()->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role = Role::find($role->id);

        if (!$role) {
            logAction('Role not found for delete', 'warning', [
                'role_id' => $role->id,
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found',
                ], 404);
            }

            return redirect()->route('roles.index')
                ->with('error', 'Role not found');
        }

        // Check if role has users assigned
        if ($role->users()->exists()) {
            logAction('Role deletion attempted but has users', 'warning', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role. It is assigned to one or more users.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Cannot delete role. It is assigned to one or more users.');
        }

        // Check if it's a protected role
        if (in_array($role->name, ['Super Admin'])) {
            logAction('Protected role deletion attempted', 'warning', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This is a system role and cannot be deleted.',
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'This is a system role and cannot be deleted.');
        }

        $oldName = $role->name;

        logAction('Role Deleted', 'info', [
            'role_id' => $role->id,
            'old_name' => $oldName,
        ]);

        $role->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully',
            ],200);
        }

        return redirect()->route('roles.index')
            ->with('status', 'Role deleted successfully');
    }
}
