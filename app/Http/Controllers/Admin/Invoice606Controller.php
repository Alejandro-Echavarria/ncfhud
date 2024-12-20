<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\Invoices606Import;
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
        ];
    }

    public function create(Request $request): Response
    {
        $clientFilter = $request?->client;
        $monthFilter = $request?->month;
        $yearFilter = $request?->year;
        $perPage = $request?->per_page;
        $page = $request?->page;

        $invoices = [];

        if ($monthFilter && $yearFilter) {
            $invoices = Invoice606::filter($clientFilter, $monthFilter, $yearFilter)
                ->orderBy('proof_date', 'asc')
                ->paginate($perPage);
        }

        return Inertia::render('Admin/Invoices/606/Create', compact(
                'invoices', 'clientFilter',
                'monthFilter', 'yearFilter',
                'perPage', 'page'
            )
        );
    }

    public function store(Request $request): void
    {
        $data = $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $excel = Excel::import(new Invoices606Import($data), $data['file']);

//        $excel = Excel::toArray(new Invoices606Import($data), $data['file']);
//
//        return response()->json(["success" => true, 'data' => $excel], 200);
    }
}
