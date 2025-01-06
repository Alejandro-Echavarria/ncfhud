<?php

namespace App\Http\Controllers\Admin\InternalAPI\V2;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Invoice606;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceCompareController extends Controller implements HasMiddleware
{
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
        $clientFilter = $request?->client;
        $monthFilter = $request?->month;
        $yearFilter = $request?->year;

        $data = $request->validate([
            'client' => 'required|exists:clients,id',
            'month' => 'required|min:1',
            'year' => 'required|min:4',
        ]);

        // Obtener los datos de las tablas 607 y 606
        $invoices607 = Invoice::select(
            'rnc',
            'ncf',
            'proof_date',
            'amount',
            'itbis',
            'third_party_itbis_withheld'
        )->filter($clientFilter, $monthFilter, $yearFilter)
            ->orderBy('proof_date', 'asc')
            ->get()
            ->toArray();

        $invoices606 = Invoice606::select(
            'rnc',
            'rnc',
            'ncf',
            'proof_date',
            'amount',
            'itbis',
            'withheld_itbis'
        )->filter($clientFilter, $monthFilter, $yearFilter)
            ->orderBy('proof_date', 'asc')
            ->get()
            ->toArray();

        $differences = []; // Para almacenar diferencias entre 607 y 606

        // Comparar las facturas 606 contra 607
        $notIn607 = array_filter($invoices606, function ($invoice606) use ($invoices607, &$differences) {
            $ncfExistsIn607 = false;

            foreach ($invoices607 as $invoice607) {
                if ($invoice607['ncf'] === $invoice606['ncf']) {
                    $ncfExistsIn607 = true;

                    $fieldsToCompare = [
                        'rnc',
                        'ncf',
                        'proof_date',
                        'amount',
                        'itbis',
                        'withheld_itbis' => 'third_party_itbis_withheld', // Mapeo de campos
                    ];

                    $rowDifferences = [];

                    foreach ($fieldsToCompare as $field606 => $field607) {
                        if (is_int($field606)) {
                            $field606 = $field607;
                        }

                        $value606 = $invoice606[$field606];
                        $value607 = $invoice607[$field607];

                        if (in_array($field606, ['amount', 'itbis', 'withheld_itbis'])) {
                            // Comparación numérica
                            if (round((float)$value606, 2) !== round((float)$value607, 2)) {
                                $rowDifferences[$field606] = [
                                    'invoices606' => $value606,
                                    'invoices607' => $value607,
                                ];
                            }
                        } else {
                            // Comparación de texto
                            if ((string)$value606 !== (string)$value607) {
                                $rowDifferences[$field606] = [
                                    'invoices606' => $value606,
                                    'invoices607' => $value607,
                                ];
                            }
                        }
                    }

                    if (!empty($rowDifferences)) {
                        $differences[] = [
                            'invoices606' => $invoice606,
                            'invoices607' => $invoice607,
                            'differences' => $rowDifferences,
                        ];
                    }
                }
            }

            return !$ncfExistsIn607;
        });

        // Comparar las facturas 607 contra 606
        $notIn606 = array_filter($invoices607, function ($invoice607) use ($invoices606) {
            foreach ($invoices606 as $invoice606) {
                if ($invoice606['ncf'] === $invoice607['ncf']) {
                    return false;
                }
            }
            return true;
        });

        return response()->json([
            'data' => [
                'invoices607' => $invoices607,
                'invoices606' => $invoices606,
                'notIn607' => array_values($notIn607),
                'notIn606' => array_values($notIn606),
                'differences' => $differences,
            ],
        ]);
    }
}
