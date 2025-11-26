<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('id', 'desc')->paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        logAction('Permission Created', 'info', [
            'permission_id'   => $permission->id,
            'permission_name' => $permission->name,
        ]);

        return redirect()->route('permissions.index')
            ->with('status', 'Permission Added Successfully');
    }

    public function edit($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
    
            logAction('Permission not found for editing', 'warning', [
                'permission_id' => $id,
            ]);
    
            return redirect()
                ->route('permissions.index')
                ->with('error', __('permissions.not_found'));
        }

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {

        $permission = Permission::find($permission->id);

        if (!$permission) {
    
            logAction('Permission not found for update', 'error', [
                'permission_id' => $permission->id,
            ]);
    
            return response()->json(['message' => 'Updated successfully']);
        }
        // Save old values
        $old = $permission->only(['name', 'description']);

        // Update
        $permission->update($request->only(['name', 'description']));

        // Log
        logAction('Permission Updated', 'info', [
            'permission_id'      => $permission->id,
            'old_name'           => $old['name'],
            'new_name'           => $permission->name,
            'old_description'    => $old['description'],
            'new_description'    => $permission->description,
        ]);

        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy(Permission $permission)
    {

        $permission = Permission::find($permission->id);

        if (!$permission) {
    
            logAction('Permission not found for delete', 'error', [
                'permission_id' => $permission->id,
            ]);
    
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission not found',
                ]);
            }
        }

        $oldName = $permission->name;

        logAction('Permission Deleted', 'warning', [
            'permission_id' => $permission->id,
            'old_name'      => $oldName,
        ]);

        $permission->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully',
            ]);
        }

        return redirect()->route('permissions.index')
            ->with('status', 'Permission deleted successfully');
    }
}
