<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
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
            'rnc' => 'required',
            'business_name' => 'required',
            'commercial_activity' => 'required',
            'email' => 'required',
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
}
