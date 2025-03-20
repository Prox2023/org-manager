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
    public function index(): Response
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::with('permissions')->get(),
        ]);
    }

    /**
     * Show the role with the specified resource.
     */
    public function show(Role $role): Response
    {
        $role->load('permissions');
        
        return Inertia::render('Admin/Roles/Show', [
            'role' => $role,
            'availablePermissions' => Permission::all(),
        ]);
    }

}
