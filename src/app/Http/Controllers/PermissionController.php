<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;

class PermissionController extends Controller
{

    public function index()
    {
        $permissions = Permission::orderBy('id', 'desc')->paginate(2);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(StorePermissionRequest $request)
    {
        Permission::create(['name' => $request->name, 'description' => $request->description]);
        return redirect()->route('permissions.index')->with('status', 'Permission Added Successfully');
    }

    public function edit(Permission $permission)
    {
        // $permission is automatically the model loaded from the ID
        return view('admin.permissions.edit', compact('permission'));
    }


    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->only(['name', 'description']));
        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);
        }

        return redirect()->route('permissions.index')
            ->with('status', 'Permission deleted successfully');
    }
}
