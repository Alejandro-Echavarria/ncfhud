<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::with('permissions:id')->paginate(10);
        $permissions = Permission::select('id', 'name', 'description')->get();

        return Inertia::render('Admin/Roles/Index', compact('roles', 'permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'permissions' => 'required|array|min:1'
        ]);

        $role = Role::create($data);

        $role->permissions()->sync($data['permissions']);

        $page = $request?->page;
        $search = $request?->search;

        return to_route('admin.roles.index', [
            'search' => $search,
            'page' => $page
        ])->with('flash', 'Rol creado');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'permissions' => 'required|array|min:1'
        ]);

        $role->update($data);

        $role->permissions()->sync($data['permissions']);

        $page = $request?->page;
        $search = $request?->search;

        return to_route('admin.roles.index', [
            'search' => $search,
            'page' => $page
        ])->with('flash', 'Rol actualizado');
    }
}
