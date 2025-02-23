<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\InvoicesImport;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceCompareController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:admin.invoices.compare', only: ['index', 'compare']),
        ];
    }

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

    public function compare(Request $request): \Illuminate\Http\JsonResponse
    {
        $clientFilter = $request?->client;
        $monthFilter = $request?->month;
        $yearFilter = $request?->year;
        $perPage = $request?->per_page;
        $page = $request?->page;

        $data = $request->validate([
            'client' => 'required|exists:clients,id',
            'month' => 'required|min:1',
            'year' => 'required|min:4',
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $data['user'] = auth()->user()->id;

        $excelData = Excel::toArray(new InvoicesImport($data), $data['file']);

        $adjustedExcelData = array_map(function ($row) {
            return [
                'rnc' => $row['rnc_cedula'],
//                'identification_type' => $row['tipo_identificacion'],
//                'ncf' => $row['numero_comprobante_fiscal'],
                'ncf' => $row['numero_de_comprobante'],
//                'ncf_modified' => $row['numero_comprobante_fiscal_modificado'],
//                'income_type' => $row['tipo_de_ingreso'],
                'proof_date' => $row['fecha_comprobante'],
//                'withholding_date' => $row['fecha_de_retencion'],
                'amount' => $row['monto_facturado'],
                'itbis' => $row['itbis_facturado'],
//                'third_party_itbis' => $row['itbis_retenido_por_terceros'],
                'third_party_itbis' => $row['itbis_retenido'],
//                'received_itbis' => $row['itbis_percibido'],
//                'third_party_income_retention' => $row['retencion_renta_por_terceros'],
//                'isr' => $row['isr_percibido'],
//                'selective_tax' => $row['impuesto_selectivo_al_consumo'],
//                'other_taxes_fees' => $row['otros_impuestostasas'],
//                'legal_tip_amount' => $row['monto_propina_legal'],
//                'cash' => $row['efectivo'],
//                'check_transfer_deposit' => $row['cheque_transferencia_deposito'],
//                'debit_credit_card' => $row['tarjeta_debitocredito'],
//                'credit_sale' => $row['venta_a_credito'],
//                'bonds_certificates' => $row['bonos_o_certificados_de_regalo'],
//                'barter' => $row['permuta'],
//                'other_sales_forms' => $row['otras_formas_de_ventas'],
            ];
        }, $excelData[0]);

        usort($adjustedExcelData, function ($a, $b) {
            return strcmp($a['ncf'], $b['ncf']);
        });

        $invoices = DB::table('invoices')
            ->select(
                'rnc',
//                'identification_type',
                'ncf',
//                'ncf_modified',
//                'income_type',
                'proof_date',
//                'withholding_date',
                'amount',
                'itbis',
                'third_party_itbis',
//                'received_itbis',
//                'third_party_income_retention',
//                'isr',
//                'selective_tax',
//                'other_taxes_fees',
//                'legal_tip_amount',
//                'cash',
//                'check_transfer_deposit',
//                'debit_credit_card',
//                'credit_sale',
//                'bonds_certificates',
//                'barter',
//                'other_sales_forms'
            )
            ->where('client_id', $clientFilter)
            ->whereMonth(
                config('app.db_driver') === 'pgsql' ?
                    DB::raw("TO_DATE(proof_date, 'YYYYMMDD')") :
                    DB::raw("STR_TO_DATE(proof_date, '%Y%m%d')"), "$monthFilter"
            )
            ->whereYear(
                config('app.db_driver') === 'pgsql' ?
                    DB::raw("TO_DATE(proof_date, 'YYYYMMDD')") :
                    DB::raw("STR_TO_DATE(proof_date, '%Y%m%d')"), "$yearFilter"
            )
            ->orderBy('ncf')
            ->get();

        $differences = []; // Arreglo para almacenar las diferencias

        $notInDatabase = array_filter($adjustedExcelData, function ($excelRow) use ($invoices, &$differences) {
            $invoices = json_decode(json_encode($invoices), true);
            $ncfExists = false;

            foreach ($invoices as $invoice) {
                if ($invoice['ncf'] === $excelRow['ncf']) {
                    $ncfExists = true;
                    $fieldsToCompare = [
                        'rnc',
//                        'identification_type',
                        'ncf',
//                        'ncf_modified',
//                        'income_type',
                        'proof_date',
//                        'withholding_date',
                        'amount',
                        'itbis',
                        'third_party_itbis',
//                        'received_itbis',
//                        'third_party_income_retention',
//                        'isr',
//                        'selective_tax',
//                        'other_taxes_fees',
//                        'legal_tip_amount',
//                        'cash',
//                        'check_transfer_deposit',
//                        'debit_credit_card',
//                        'credit_sale',
//                        'bonds_certificates',
//                        'barter',
//                        'other_sales_forms'
                    ];

                    $rowDifferences = [];

                    foreach ($fieldsToCompare as $field) {
                        $excelValue = $excelRow[$field];
                        $invoiceValue = $invoice[$field];

                        if (in_array($field, [
                            'amount',
                            'itbis',
                            'third_party_itbis',
//                            'received_itbis',
//                            'third_party_income_retention',
//                            'isr',
//                            'selective_tax',
//                            'other_taxes_fees',
//                            'legal_tip_amount',
//                            'cash', 'check_transfer_deposit', 'debit_credit_card', 'credit_sale', 'bonds_certificates', 'barter', 'other_sales_forms'
                        ])) {
                            // Comparación de valores numéricos
                            if (round((float)$excelValue, 2) !== round((float)$invoiceValue, 2)) {
                                $rowDifferences[$field] = ['excel' => $excelValue, 'database' => $invoiceValue];
                            }
                        } else {
                            // Comparación de valores de cadena
                            if ((string)$excelValue !== (string)$invoiceValue) {
                                $rowDifferences[$field] = ['excel' => $excelValue, 'database' => $invoiceValue];
                            }
                        }
                    }

                    if (!empty($rowDifferences)) {
                        $differences[] = ['excel_row' => $excelRow, 'differences' => $rowDifferences];
                    }
                }
            }

            return !$ncfExists;
        });

        $invoicesArray = json_decode(json_encode($invoices), true);

        // Comparar datos de la base de datos con el Excel
        $notInExcel = array_filter($invoicesArray, function ($invoice) use ($adjustedExcelData) {
            $ncfExistsInExcel = false;

            foreach ($adjustedExcelData as $excelRow) {
                if ($invoice['ncf'] === $excelRow['ncf']) {
                    $ncfExistsInExcel = true;
                    break;
                }
            }

            return !$ncfExistsInExcel;
        });

        return response()->json([
            'data' => [
                'excel' => $excelData,
                'invoices' => $invoices,
                'adjusted_excel' => $adjustedExcelData,
                'not_in_excel' => $notInExcel,
                'not_in_database' => array_values($notInDatabase),
                'differences' => $differences,
            ]
        ]);
    }
}
