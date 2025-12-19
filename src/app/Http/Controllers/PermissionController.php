<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Services\PermissionService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;

use DomainException;
class PermissionController extends BaseController
{

    public function __construct(
        protected PermissionService $permissionService
    ) {}

    public function index()
    {
        $permissions = Permission::orderBy('id', 'desc')->paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function getAll(Request $request): JsonResponse
    {

        // 1. Authorize the action using the PermissionPolicy
        // This will throw an AuthorizationException (403 Forbidden) if the user is not authorized
        // to view any categories (i.e., viewAny returns false).
        $this->authorize('viewAny', Permission::class);

        // 2. Get the authenticated user.
        $user = Auth::user();

        // Safety check: If the route is not protected by 'auth' middleware, $user could be null.
        // We ensure the user is an instance of the User model before passing it to the service.
        if (!$user instanceof User) {
            // If the user is not authenticated, we throw an exception.
            // In a real Laravel app, the 'auth' middleware should handle this,
            // but this check adds robustness.
            abort(401, 'Unauthenticated.');
        }

        try {
            $perPage            = $request->get('per_page', NULL);
            $data               = $this->permissionService->getPaginated($perPage);
            $draw               = $request->get('draw', 1);
            $response           = PermissionResource::collection($data)->response()->getData(true);
            $response['draw']   = (int) $draw;
            $response['recordsTotal'] = $response['meta']['total'];
            $response['recordsFiltered'] = $response['meta']['total'];
            return response()->json($response);
        } catch (ValidationException $e) {
            // This catch block is technically redundant if using a FormRequest,
            // as the FormRequest handles the redirect/JSON response automatically.
            // However, it's kept here for clarity if you ever validate manually.
            logAction('Validation Error', 'error', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation Failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (DomainException $e) {
            // This is a good exception to catch here, as it's a specific
            // business logic failure that the controller should know how to report.
            logAction('Domain Error', 'error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function create()
    {
        $this->authorize('create', Permission::class);
        return view('admin.permissions.create');
    }

    public function store(StorePermissionRequest $request)
    {
        $this->authorize('create', Permission::class);

        $permission = Permission::create([
            'name'        => $request->name,
            'description' => $request->description,
            'module' => $request->module,
        ]);

        logAction('Permission Created', 'info', [
            'permission_id'   => $permission->id,
            'permission_name' => $permission->name,
            'permission_module' => $permission->module,
        ]);

        return redirect()->route('permissions.create')
            ->with('status', 'Permission Added Successfully');
    }

    public function edit(Permission $permission)
    {
        $this->authorize('update', $permission);

        if (!$permission) {
    
            logAction('Permission not found for editing', 'warning', [
                'permission_id' => $permission->id,
            ]);
    
            return redirect()
                ->route('permissions.index')
                ->with('error', __('permissions.not_found'));
        }

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $permission = Permission::find($permission->id);

        if (!$permission) {
    
            logAction('Permission not found for update', 'error', [
                'permission_id' => $permission->id,
            ]);
    
            return response()->json(['message' => 'Updated successfully']);
        }
        // Save old values
        $old = $permission->only(['name', 'description', 'module']);

        // Update
        $permission->update($request->only(['name', 'description', 'module']));

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
        $this->authorize('delete', $permission);

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
