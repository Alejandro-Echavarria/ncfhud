<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::select('name', 'email', 'created_at', 'updated_at')->paginate(10);

        return Inertia::render('Admin/Users/Index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        return to_route('admin.users.index')->with('flash', 'User created');
    }
}
