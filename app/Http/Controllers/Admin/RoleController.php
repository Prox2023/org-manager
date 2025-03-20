<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Role::with('permissions');

        // Handle search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('guard_name', 'like', "%{$search}%");
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['name', 'guard_name', 'created_at', 'updated_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
        }
        
        $query->orderBy($sortField, $sortDirection);

        // Get paginated roles
        $roles = $query->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the role with the specified resource.
     */
    public function show(Request $request, Role $role): Response
    {
        $role->load('permissions');
        
        $query = Permission::query();
        
        // Handle search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('guard_name', 'like', "%{$search}%");
        }
        
        // Get paginated permissions
        $permissions = $query->paginate(10)
            ->withQueryString();
        
        return Inertia::render('Admin/Roles/Show', [
            'role' => $role,
            'permissions' => $permissions,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Roles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'required|string|max:255',
        ]);

        $role = Role::create($validated);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::with('permissions')->get(),
            'flash' => [
                'success' => 'Role created successfully.'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): Response
    {
        // Prevent deletion of system roles
        if ($role->name === 'admin' || $role->name === 'user') {
            return Inertia::render('Admin/Roles/Index', [
                'roles' => Role::with('permissions')->get(),
                'flash' => [
                    'error' => 'System roles cannot be deleted.'
                ]
            ]);
        }

        $role->delete();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::with('permissions')->get(),
            'flash' => [
                'success' => 'Role deleted successfully.'
            ]
        ]);
    }

    /**
     * Toggle a permission for a role.
     */
    public function togglePermission(Request $request, Role $role, Permission $permission)
    {
        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
            $message = 'Permission revoked successfully.';
        } else {
            $role->givePermissionTo($permission);
            $message = 'Permission granted successfully.';
        }

        // Rebuild the query with the same filters
        $query = Permission::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('guard_name', 'like', "%{$search}%");
        }
        
        $permissions = $query->paginate(10)
            ->withQueryString();

        return back()->with('success', $message);
    }
}
