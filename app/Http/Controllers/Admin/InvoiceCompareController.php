<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\InvoicesImport;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceCompareController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        $clients = Client::selectRaw("id, CONCAT(rnc, ' - ', business_name, ' - ', commercial_activity) AS business_name")->get();
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

        return Inertia::render('Admin/Invoices/Compare', compact('clients', 'invoices', 'clientFilter', 'monthFilter', 'yearFilter', 'page', 'perPage'));
    }

    public function compare(Request $request)
    {
        $data = $request ->validate([
            'client' => 'required',
            'month' => 'required',
            'year' => 'required',
            'file' => 'required'
        ]);

        $excel = Excel::toArray(new InvoicesImport($data), $data['file']);

        return response()->json(["success" => true, 'data' => $excel], 200);
    }
}
