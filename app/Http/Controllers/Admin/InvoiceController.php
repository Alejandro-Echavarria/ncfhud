<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\InvoicesImport;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:admin.invoices.index', only: ['index']),
            new Middleware('permission:admin.invoices.create_607', only: ['create', 'store']),
            new Middleware('permission:admin.invoices.destroy_607', only: ['delete', 'destroy']),
        ];
    }

    public function index(Request $request): \Inertia\Response
    {
        $clientFilter = $request?->client;
        $monthFilter = $request?->month;
        $yearFilter = $request?->year;
        $perPage = $request?->per_page;
        $page = $request?->page;

        $invoices = [];
        if ($clientFilter && $monthFilter && $yearFilter) {
            $invoices = Invoice::filter($clientFilter, $monthFilter, $yearFilter)
                ->orderBy('proof_date', 'asc')
                ->paginate($perPage);
        }

        $clients = Client::selectRaw("id, CONCAT(rnc, ' - ', business_name, ' - ', commercial_activity) AS business_name")->get();

        return Inertia::render('Admin/Invoices/Index', compact('clients', 'invoices', 'clientFilter', 'monthFilter', 'yearFilter', 'page', 'perPage'));
    }

    public function create(Request $request): Response
    {
        $clients = Client::selectRaw(
            "id, CONCAT(rnc, ' - ', business_name, ' - ', commercial_activity) AS business_name"
        )->get();

        $clientFilter = $request?->client;
        $monthFilter = $request?->month;
        $yearFilter = $request?->year;
        $perPage = $request?->per_page;
        $page = $request?->page;

        $client = [];
        $invoices = [];
        if ($clientFilter && $monthFilter && $yearFilter) {
            $client = Client::select('id', 'business_name', 'commercial_activity')
                ->where('id', $clientFilter)
                ->get();

            $invoices = Invoice::filter($clientFilter, $monthFilter, $yearFilter)
                ->orderBy('proof_date', 'asc')
                ->paginate($perPage);
        }

        return Inertia::render('Admin/Invoices/Create', compact(
                'invoices', 'clients', 'client', 'clientFilter', 'monthFilter', 'yearFilter', 'page', 'perPage'
            )
        );
    }

    public function store(Request $request): void
    {
        $data = $request->validate([
            'client' => 'required|exists:clients,id',
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $data['user'] = auth()->user()->id;

        Excel::import(new InvoicesImport($data), $data['file']);
    }

    public function delete(Request $request): Response
    {
        $clients = Client::selectRaw(
            "id, CONCAT(rnc, ' - ', business_name, ' - ', commercial_activity) AS business_name"
        )->get();

        $clientFilter = $request?->client;
        $monthFilter = $request?->month;
        $yearFilter = $request?->year;
        $perPage = $request?->per_page;
        $page = $request?->page;

        $client = [];
        $invoices = [];

        if ($clientFilter && $monthFilter && $yearFilter) {
            $client = Client::select('id', 'business_name', 'commercial_activity')
                ->where('id', $clientFilter)
                ->get();

            $invoices = Invoice::filter($clientFilter, $monthFilter, $yearFilter)
                ->orderBy('proof_date', 'asc')
                ->paginate($perPage);
        }

        return Inertia::render('Admin/Invoices/607/Delete',
            compact(
                'invoices', 'clients', 'client',
                'clientFilter', 'monthFilter', 'yearFilter', 'page', 'perPage'
            )
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client' => 'required|exists:clients,id',
            'month' => 'required|between:1,12',
            'year' => 'required|digits:4',
        ]);

        Invoice::filter($data['client'], $data['month'], $data['year'])
            ->delete();

        return to_route('admin.invoices.delete', [
            'client' => $data['client'],
            'month' => $data['month'],
            'year' => $data['year'],
            'per_page' => '10'
        ])->with('flash', 'Facturas eliminadas');
    }
}
