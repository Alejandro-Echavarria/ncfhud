<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::paginate(10);
        $permissions = Permission::select('id', 'name', 'description')->get();

        return Inertia::render('Admin/Roles/Index', compact('roles', 'permissions'));
    }
}
