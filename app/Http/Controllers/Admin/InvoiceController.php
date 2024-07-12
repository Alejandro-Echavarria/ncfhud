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
//        return response()->json(["success" => true, 'data' => Invoice::all()], 200);
//        $excel = Excel::import(new InvoicesImport, "C:\Users\mechavarria\OneDrive - aldereca.com\Desktop\Book1.xlsx");
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

        $clients = Client::select('id', 'business_name')->get();

        return Inertia::render('Admin/Invoices/Index', compact('clients', 'invoices', 'clientFilter', 'monthFilter', 'yearFilter', 'page', 'perPage'));
    }

    public function create(): \Inertia\Response
    {
        $clients = Client::select('id', 'business_name')->get();

        return Inertia::render('Admin/Invoices/Create', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            ''
        ]);


    }
}
