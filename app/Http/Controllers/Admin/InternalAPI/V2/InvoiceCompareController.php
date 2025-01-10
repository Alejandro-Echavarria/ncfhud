<?php

namespace App\Http\Controllers\Admin\InternalAPI\V2;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Invoice606;
use App\Services\InvoiceComparisonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceCompareController extends Controller implements HasMiddleware
{
    private InvoiceComparisonService $comparisonService;

    public function __construct(InvoiceComparisonService $comparisonService)
    {
        $this->comparisonService = $comparisonService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:admin.invoices.compare', only: ['index', 'compare']),
        ];
    }

    public function index(Request $request): Response
    {
        $clients = Client::selectRaw("
            id, CONCAT(rnc, ' - ', business_name, ' - ', commercial_activity) AS business_name"
        )->get();

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

        return Inertia::render('Admin/Invoices/Compare/V2/Compare',
            compact('clients', 'invoices', 'clientFilter', 'monthFilter', 'yearFilter', 'page', 'perPage'));
    }

    public function compare(Request $request): JsonResponse
    {
        $this->validateRequest($request);

        $clientFilter = $request->client;
        $monthFilter = $request->month;
        $yearFilter = $request->year;

        // Obtener facturas
        $invoices607 = $this->getInvoices607($clientFilter, $monthFilter, $yearFilter);
        $invoices606 = $this->getInvoices606($clientFilter, $monthFilter, $yearFilter);

        // Realizar comparaciones
        $comparisonResults = $this->comparisonService->compareInvoices($invoices606, $invoices607);

        return response()->json([
            'data' => $comparisonResults,
        ]);
    }

    private function validateRequest(Request $request): void
    {
        $request->validate([
            'client' => 'required|exists:clients,id',
            'month' => 'required|min:1',
            'year' => 'required|min:4',
        ]);
    }

    private function getInvoices607($client, $month, $year): array
    {
        return Invoice::select(
            'rnc',
            'ncf',
            'proof_date',
            'amount',
            'itbis',
            'third_party_itbis_withheld'
        )->filter($client, $month, $year)
            ->orderBy('proof_date', 'asc')
            ->get()
            ->toArray();
    }

    private function getInvoices606($client, $month, $year): array
    {
        return Invoice606::select(
            'rnc',
            'ncf',
            'proof_date',
            'amount',
            'itbis',
            'withheld_itbis'
        )->filter($client, $month, $year)
            ->orderBy('proof_date', 'asc')
            ->get()
            ->toArray();
    }
}
