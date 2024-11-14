<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $page = $request?->page;
        $filter = $request?->search;

        $users = User::filter($filter)
            ->select('id', 'name', 'email', 'is_active', 'created_at', 'updated_at')
            ->where('id', '!=', '1')
            ->paginate(10);

        return Inertia::render('Admin/Users/Index', compact('page', 'filter', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users|email:rfc,dns|max:255',
            'password' => 'required|max:255|min:4',
            'is_active' => 'required|boolean'
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        $page = $request?->page;
        $search = $request?->search;

        return to_route('admin.users.index', [
            'search' => $search,
            'page' => $page
        ])->with('flash', 'User created');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->id === 1) {
            return redirect()->back()->withErrors(['id' => 'Este usuario no puede ser editado.']);
        }

        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users,email,' . $user->id . '|email:rfc,dns|max:255',
            'password' => 'nullable|max:255|min:4', // Hacer que el campo sea opcional
            'is_active' => 'required|boolean'
        ]);

        // Si el campo 'password' está vacío, elimínalo del array de datos
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            // Si está presente, encripta la contraseña
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        $page = $request?->page;
        $search = $request?->search;

        return to_route('admin.users.index', [
            'search' => $search,
            'page' => $page
        ])->with('flash', 'User updated');
    }
}
