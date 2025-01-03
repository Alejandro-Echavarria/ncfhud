<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:admin.roles.index', only: ['index']),
            new Middleware('permission:admin.roles.create', only: ['store']),
            new Middleware('permission:admin.roles.edit', only: ['update']),
            new Middleware('permission:admin.roles.destroy', only: ['destroy']),
        ];
    }

    public function index(): Response
    {
        $roles = Role::with('permissions:id')->paginate(10);
        $permissions = Permission::select(
            'id',
            'name',
            'description',
            'type',
            'order'
        )
            ->orderBy('type')
            ->orderBy('order')
            ->get()
            ->groupBy('type');

        return Inertia::render('Admin/Roles/Index', compact('roles', 'permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|max:255|unique:roles,name',
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
            'name' => 'required|max:255|unique:roles,name,' . $role->id,
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

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return to_route('admin.roles.index')->withErrors([
                'delete' => 'Este rol no puede ser eliminado porque está asignado a uno o más usuarios.'
            ]);
        }

        $role->delete();

        return to_route('admin.roles.index')->with('flash', 'Rol eliminado');
    }
}
