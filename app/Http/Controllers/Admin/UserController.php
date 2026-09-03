<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\FleetCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.access')->only(['index', 'data', 'show', 'edit']);
        $this->middleware('permission:users.create')->only(['store']);
        $this->middleware('permission:users.edit')->only(['update', 'toggle']);
        $this->middleware('permission:users.delete')->only(['destroy']);
    }

    public function index(): View
    {
        return view('content.apps.app-user-list', $this->formPayload() + [
            'stats' => [
                'total' => User::count(),
                'roles' => Role::count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
            ],
        ]);
    }

    public function data(): JsonResponse
    {
        $users = User::query()->with(['roles', 'projects'])->orderBy('name')->get();

        return response()->json([
            'data' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'full_name' => $user->name ?: $user->username,
                'username' => $user->username,
                'email' => $user->email ?: '-',
                'role' => $user->roles->pluck('name')->first() ?? '',
                'current_plan' => $user->projects->pluck('project_code')->implode(', ') ?: '-',
                'billing' => $user->username,
                'status' => $user->is_active ? 'Active' : 'Inactive',
                'avatar' => '',
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $this->guardAdministratorRole($data['roles']);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'],
        ]);
        $user->syncRoles($data['roles']);
        $user->syncProjectCodes($data['sites'] ?? []);

        return response()->json('Created');
    }

    public function show(User $user): View
    {
        $user->load(['roles', 'projects']);

        return view('content.apps.app-user-view-account', $this->formPayload() + [
            'user' => $user,
            'permissions' => $user->getAllPermissions(),
        ]);
    }

    public function edit(User $user): JsonResponse
    {
        $user->load(['roles', 'projects']);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'roles' => $user->roles->pluck('name')->all(),
            'sites' => $user->projectCodes(),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $this->validated($request, $user->id);
        $this->guardAdministratorRole($data['roles']);

        $user->fill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'is_active' => $data['is_active'],
        ]);
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        $user->syncRoles($data['roles']);
        $user->syncProjectCodes($data['sites'] ?? []);

        return response()->json('Updated');
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }
        if ($user->hasRole('administrator') && ! auth()->user()->hasRole('administrator')) {
            return response()->json(['message' => 'Only administrators can delete administrator users.'], 422);
        }

        $user->delete();

        return response()->json('Deleted');
    }

    public function toggle(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot suspend your own account.'], 422);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return response()->json($user->is_active ? 'Activated' : 'Suspended');
    }

    private function validated(Request $request, ?int $userId = null): array
    {
        $unique = fn (string $column) => Rule::unique('users', $column)->ignore($userId);

        $request->merge([
            'email' => $request->filled('email') ? $request->input('email') : null,
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash', 'min:3', 'max:50', $unique('username')],
            'email' => ['nullable', 'email', 'max:255', $unique('email')],
            'password' => [$userId ? 'nullable' : 'required', 'string', 'min:5'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
            'sites' => ['nullable', 'array'],
            'sites.*' => ['string', 'max:10'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    private function guardAdministratorRole(array $roles): void
    {
        if (in_array('administrator', $roles, true) && ! auth()->user()->hasRole('administrator')) {
            abort(403, 'Only administrators can assign the administrator role.');
        }
    }

    private function formPayload(): array
    {
        return [
            'roles' => Role::orderBy('name')->pluck('name'),
            'sites' => app(FleetCache::class)->codesForUserForm(),
        ];
    }
}
