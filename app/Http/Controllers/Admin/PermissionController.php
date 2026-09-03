<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permissions.access')->only(['index', 'data', 'show']);
        $this->middleware('permission:permissions.create')->only(['store']);
        $this->middleware('permission:permissions.edit')->only(['update']);
        $this->middleware('permission:permissions.delete')->only(['destroy']);
    }

    public function index(): View
    {
        return view('content.apps.app-access-permission', [
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function data(): JsonResponse
    {
        $permissions = Permission::query()->with('roles')->orderBy('name')->get();

        return response()->json([
            'data' => $permissions->map(fn (Permission $permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'assigned_to' => $permission->roles->pluck('name')->all(),
                'created_date' => $permission->created_at?->format('d M Y') ?? '-',
            ]),
        ]);
    }

    public function show(Permission $permission): JsonResponse
    {
        return response()->json([
            'id' => $permission->id,
            'name' => $permission->name,
            'roles' => $permission->roles->pluck('name')->all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $permission = Permission::create(['name' => $data['name']]);
        $permission->syncRoles($data['roles'] ?? []);

        return response()->json('Created');
    }

    public function update(Request $request, Permission $permission): JsonResponse
    {
        $data = $this->validated($request, $permission->id);
        $permission->name = $data['name'];
        $permission->save();
        $permission->syncRoles($data['roles'] ?? []);

        return response()->json('Updated');
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return response()->json('Deleted');
    }

    private function validated(Request $request, ?int $permissionId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9._-]+$/', Rule::unique('permissions', 'name')->ignore($permissionId)],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ], [
            'name.regex' => 'Permission name may only contain lowercase letters, numbers, dots, dashes, and underscores.',
        ]);
    }
}
