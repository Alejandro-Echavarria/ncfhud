<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\InvoicesImport;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function compare(Request $request): \Illuminate\Http\JsonResponse
    {
        $clientFilter = $request?->client;
        $monthFilter = $request?->month;
        $yearFilter = $request?->year;
        $perPage = $request?->per_page;
        $page = $request?->page;

        $data = $request->validate([
            'client' => 'required',
            'month' => 'required',
            'year' => 'required',
            'file' => 'required'
        ]);

        $excelData = Excel::toArray(new InvoicesImport($data), $data['file']);

        $adjustedExcelData = array_map(function ($row) {
            return [
                'rnc' => $row['rnccedula_o_pasaporte'],
                'identification_type' => $row['tipo_identificacion'],
                'ncf' => $row['numero_comprobante_fiscal'],
                'ncf_modified' => $row['numero_comprobante_fiscal_modificado'],
                'income_type' => $row['tipo_de_ingreso'],
                'proof_date' => $row['fecha_comprobante'],
                'withholding_date' => $row['fecha_de_retencion'],
                'amount' => $row['monto_facturado'],
                'itbis' => $row['itbis_facturado'],
                'third_party_itbis' => $row['itbis_retenido_por_terceros'],
                'received_itbis' => $row['itbis_percibido'],
                'third_party_income_retention' => $row['retencion_renta_por_terceros'],
                'isr' => $row['isr_percibido'],
                'selective_tax' => $row['impuesto_selectivo_al_consumo'],
                'other_taxes_fees' => $row['otros_impuestostasas'],
                'legal_tip_amount' => $row['monto_propina_legal'],
                'cash' => $row['efectivo'],
                'check_transfer_deposit' => $row['cheque_transferencia_deposito'],
                'debit_credit_card' => $row['tarjeta_debitocredito'],
                'credit_sale' => $row['venta_a_credito'],
                'bonds_certificates' => $row['bonos_o_certificados_de_regalo'],
                'barter' => $row['permuta'],
                'other_sales_forms' => $row['otras_formas_de_ventas'],
            ];
        }, $excelData[0]);

        usort($adjustedExcelData, function ($a, $b) {
            return strcmp($a['ncf'], $b['ncf']);
        });

        $invoices = DB::table('invoices')
            ->select(
                'rnc',
                'identification_type',
                'ncf',
                'ncf_modified',
                'income_type',
                'proof_date',
                'withholding_date',
                'amount',
                'itbis',
                'third_party_itbis',
                'received_itbis',
                'third_party_income_retention',
                'isr',
                'selective_tax',
                'other_taxes_fees',
                'legal_tip_amount',
                'cash',
                'check_transfer_deposit',
                'debit_credit_card',
                'credit_sale',
                'bonds_certificates',
                'barter',
                'other_sales_forms'
            )
            ->where('client_id', $clientFilter)
            ->whereMonth(
                config('app.db_driver') === 'pgsql' ?
                    DB::raw("TO_DATE(proof_date, 'YYYYMMDD')") :
                    DB::raw("STR_TO_DATE(proof_date, '%Y-%m-%d')"), "$monthFilter"
            )
            ->whereYear(
                config('app.db_driver') === 'pgsql' ?
                    DB::raw("TO_DATE(proof_date, 'YYYYMMDD')") :
                    DB::raw("STR_TO_DATE(proof_date, '%Y-%m-%d')"), "$yearFilter"
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
                        'rnc', 'identification_type', 'ncf', 'ncf_modified', 'income_type',
                        'proof_date', 'withholding_date', 'amount', 'itbis', 'third_party_itbis',
                        'received_itbis', 'third_party_income_retention', 'isr', 'selective_tax',
                        'other_taxes_fees', 'legal_tip_amount', 'cash', 'check_transfer_deposit',
                        'debit_credit_card', 'credit_sale', 'bonds_certificates', 'barter',
                        'other_sales_forms'
                    ];

                    $rowDifferences = [];

                    foreach ($fieldsToCompare as $field) {
                        $excelValue = $excelRow[$field];
                        $invoiceValue = $invoice[$field];

                        if (in_array($field, ['amount', 'itbis', 'third_party_itbis', 'received_itbis', 'third_party_income_retention', 'isr', 'selective_tax', 'other_taxes_fees', 'legal_tip_amount', 'cash', 'check_transfer_deposit', 'debit_credit_card', 'credit_sale', 'bonds_certificates', 'barter', 'other_sales_forms'])) {
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

//        $notInDatabase = array_filter($adjustedExcelData, function ($excelRow) use ($invoices) {
//            $invoices = json_decode(json_encode($invoices), true);
//
//            foreach ($invoices as $invoice) {
//                if (
//                    $invoice['rnc'] === $excelRow['rnc'] &&
//                    $invoice['identification_type'] === $excelRow['identification_type'] &&
//                    $invoice['ncf'] === $excelRow['ncf'] &&
//                    $invoice['ncf_modified'] === $excelRow['ncf_modified'] &&
//                    $invoice['income_type'] === $excelRow['income_type'] &&
//                    $invoice['proof_date'] == $excelRow['proof_date'] &&
//                    $invoice['withholding_date'] == $excelRow['withholding_date'] &&
//                    $this->getOrZero($invoice['amount']) ===  $this->getOrZero($excelRow['amount']) &&
//                    $this->getOrZero($invoice['itbis']) ===  $this->getOrZero($excelRow['itbis']) &&
//                    $this->getOrZero($invoice['third_party_itbis']) === $this->getOrZero($excelRow['third_party_itbis']) &&
//                    $this->getOrZero($invoice['received_itbis']) === $this->getOrZero($excelRow['received_itbis']) &&
//                    $this->getOrZero($invoice['third_party_income_retention']) === $this->getOrZero($excelRow['third_party_income_retention']) &&
//                    $this->getOrZero($invoice['isr']) === $this->getOrZero($excelRow['isr']) &&
//                    $this->getOrZero($invoice['selective_tax']) === $this->getOrZero($excelRow['selective_tax']) &&
//                    $this->getOrZero($invoice['other_taxes_fees']) === $this->getOrZero($excelRow['other_taxes_fees']) &&
//                    $this->getOrZero($invoice['legal_tip_amount']) === $this->getOrZero($excelRow['legal_tip_amount']) &&
//                    $this->getOrZero($invoice['cash']) === $this->getOrZero($excelRow['cash']) &&
//                    $this->getOrZero($invoice['check_transfer_deposit']) === $this->getOrZero($excelRow['check_transfer_deposit']) &&
//                    $this->getOrZero($invoice['debit_credit_card']) === $this->getOrZero($excelRow['debit_credit_card']) &&
//                    $this->getOrZero($invoice['credit_sale']) === $this->getOrZero($excelRow['credit_sale']) &&
//                    $this->getOrZero($invoice['bonds_certificates']) === $this->getOrZero($excelRow['bonds_certificates']) &&
//                    $this->getOrZero($invoice['barter']) === $this->getOrZero($excelRow['barter']) &&
//                    $this->getOrZero($invoice['other_sales_forms']) === $this->getOrZero($excelRow['other_sales_forms'])
//                ) {
//                    return false;
//                }
//            }
//            return true;
//        });

        return response()->json([
            'data' => [
                'excel' => $excelData,
                'invoices' => $invoices,
                'adjusted_excel' => $adjustedExcelData,
//                'not_in_database' => $notInDatabase,
                'not_in_database' => array_values($notInDatabase),
                'differences' => $differences,
            ]
        ]);
    }

    public function getOrZero($value)
    {
        return $value !== null
            ? number_format($value, 2, '.', ',')
            : number_format(0, 2, '.', ',');
    }
}
