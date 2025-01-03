<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class ClientController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:admin.clients.index', only: ['index']),
            new Middleware('permission:admin.clients.create', only: ['store']),
            new Middleware('permission:admin.clients.edit', only: ['update']),
        ];
    }

    public function index(Request $request): \Inertia\Response
    {
        $page = $request?->page;
        $filter = $request?->search;

        $clients = Client::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Clients/Index', compact('page', 'filter', 'clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'rnc' => 'required|max:11|unique:clients,rnc',
            'business_name' => 'required|max:150',
            'commercial_activity' => 'required|max:150',
            'email' => 'required|max:255',
        ]);

        $data['created_by_user_id'] = auth()->user()->id;
        $client = Client::create($data);

        $page = $request?->page;
        $search = $request?->search;

        return to_route('admin.clients.index', [
            'search' => $search,
            'page' => $page
        ])->with('flash', 'Client created');
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'rnc' => 'required|max:11|unique:clients,rnc,' . $client->id,
            'business_name' => 'required|max:150',
            'commercial_activity' => 'required|max:150',
            'email' => 'required|max:255',
        ]);

        $client = $client->update($data);

        $page = $request?->page;
        $search = $request?->search;

        return to_route('admin.clients.index', [
            'search' => $search,
            'page' => $page
        ])->with('flash', 'Cliente actualizado');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $invoicesCount = number_format($client->invoices()->count());

        if ($invoicesCount) {
            return to_route('admin.clients.index')->withErrors([
                'delete' => "Este cliente no puede ser eliminado porque tiene  $invoicesCount facturas asociadas."
            ]);
        }

        $client->delete();
        return redirect()->back()->with('flash', 'Client deleted');
    }
}
