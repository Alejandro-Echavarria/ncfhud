<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\Invoices606Import;
use App\Models\Client;
use App\Models\Invoice606;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class Invoice606Controller extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:admin.invoices.create_606', only: ['create', 'store']),
            new Middleware('permission:admin.invoices.destroy_606', only: ['delete', 'destroy']),
        ];
    }

    public function create(Request $request): Response
    {
        $clients = Client::getAllCombinedDetails();

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

            $invoices = Invoice606::filter($clientFilter, $monthFilter, $yearFilter)
                ->orderBy('proof_date', 'asc')
                ->paginate($perPage);
        }

        return Inertia::render('Admin/Invoices/606/Create', compact(
                'invoices', 'clients', 'client',
                'clientFilter', 'monthFilter', 'yearFilter', 'page', 'perPage'
            )
        );
    }

    public function store(Request $request): void
    {
        $data = $request->validate([
            'client' => 'required|exists:clients,id',
            'file' => 'required|mimes:xlsx,xls',
            'month' => 'required|between:1,12',
            'year' => 'required|digits:4',
        ]);

        $data['user'] = auth()->user()->id;

        Excel::import(new Invoices606Import($data), $data['file']);
    }

    public function delete(Request $request): Response
    {
        $clients = Client::getAllCombinedDetails();

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

            $invoices = Invoice606::filter($clientFilter, $monthFilter, $yearFilter)
                ->orderBy('proof_date', 'asc')
                ->paginate($perPage);
        }

        return Inertia::render('Admin/Invoices/606/Delete',
            compact(
                'invoices', 'clients', 'client',
                'clientFilter', 'monthFilter', 'yearFilter', 'page', 'perPage'
            )
        );
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'client' => 'required|exists:clients,id',
            'month' => 'required|between:1,12',
            'year' => 'required|digits:4',
        ]);

        Invoice606::filter($data['client'], $data['month'], $data['year'])
            ->delete();

        return to_route('admin.invoices606.delete', [
            'client' => $data['client'],
            'month' => $data['month'],
            'year' => $data['year'],
            'per_page' => '10'
        ])->with('flash', 'Facturas eliminadas');
    }
}
