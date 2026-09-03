<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.access')->only(['index', 'show']);
        $this->middleware('permission:roles.create')->only(['store']);
        $this->middleware('permission:roles.edit')->only(['update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    public function index(): View
    {
        $roles = Role::query()->with('users')->withCount('users')->orderBy('name')->get();
        $permissionGroups = Permission::orderBy('name')->get()->groupBy(fn (Permission $p) => explode('.', $p->name)[0]);

        return view('content.apps.app-access-roles', [
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return response()->json('Created');
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $data = $this->validated($request, $role->id);
        if ($role->name === 'administrator' && $data['name'] !== 'administrator') {
            return response()->json(['message' => 'The administrator role cannot be renamed.'], 422);
        }
        $role->name = $data['name'];
        $role->save();
        $role->syncPermissions($data['permissions'] ?? []);

        return response()->json('Updated');
    }

    public function destroy(Role $role): JsonResponse
    {
        if ($role->name === 'administrator') {
            return response()->json(['message' => 'The administrator role cannot be deleted.'], 422);
        }
        $role->delete();

        return response()->json('Deleted');
    }

    private function validated(Request $request, ?int $roleId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/', Rule::unique('roles', 'name')->ignore($roleId)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.regex' => 'Role name may only contain lowercase letters, numbers, and underscores.',
        ]);
    }
}
