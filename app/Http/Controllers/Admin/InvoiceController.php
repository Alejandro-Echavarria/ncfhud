<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\InvoicesImport;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
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

    public function create(Request $request): \Inertia\Response
    {
        $clients = Client::selectRaw("id, CONCAT(rnc, ' - ', business_name, ' - ', commercial_activity) AS business_name")->get();
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

        return Inertia::render('Admin/Invoices/Create', compact('invoices', 'clients', 'client', 'clientFilter', 'monthFilter', 'yearFilter', 'page', 'perPage'));
    }

    public function store(Request $request): void
    {
        $data = $request->validate([
            'client' => 'required',
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $excel = Excel::import(new InvoicesImport($data), $data['file']);
    }
}
