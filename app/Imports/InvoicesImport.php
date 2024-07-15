<?php

namespace App\Imports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithValidation;

class InvoicesImport implements ToModel, WithValidation, WithHeadingRow, WithCalculatedFormulas
{
    private int $userId;
    private int $clientId;

    public function __construct($data)
    {
        $this->userId = Auth()->user()->id;
        $this->clientId = $data['client'];
    }

    public function model(array $row): Invoice
    {
        // Función para obtener el valor o cero si el valor es nulo
        $getOrZero = fn($value) => $value !== null ? round((float)$value, 2) : 0;

        return new Invoice([
            'user_id' => $this->userId,
            'client_id' => $this->clientId,
            'rnc' => $row['rnccedula_o_pasaporte'],
            'identification_type' => $row['tipo_identificacion'],
            'ncf' => $row['numero_comprobante_fiscal'],
            'ncf_modified' => $row['numero_comprobante_fiscal_modificado'],
            'income_type' => $row['tipo_de_ingreso'],
            'proof_date' => $row['fecha_comprobante'],
            'withholding_date' => $row['fecha_de_retencion'],
            'amount' => $getOrZero($row['monto_facturado']),
            'itbis' => $getOrZero($row['itbis_facturado']),
            'third_party_itbis' => $getOrZero($row['itbis_retenido_por_terceros']),
            'received_itbis' => $getOrZero($row['itbis_percibido']),
            'third_party_income_retention' => $getOrZero($row['retencion_renta_por_terceros']),
            'isr' => $getOrZero($row['isr_percibido']),
            'selective_tax' => $getOrZero($row['impuesto_selectivo_al_consumo']),
            'other_taxes_fees' => $getOrZero($row['otros_impuestostasas']),
            'legal_tip_amount' => $getOrZero($row['monto_propina_legal']),
            'cash' => $getOrZero($row['efectivo']),
            'check_transfer_deposit' => $getOrZero($row['cheque_transferencia_deposito']),
            'debit_credit_card' => $getOrZero($row['tarjeta_debitocredito']),
            'credit_sale' => $getOrZero($row['venta_a_credito']),
            'bonds_certificates' => $getOrZero($row['bonos_o_certificados_de_regalo']),
            'barter' => $getOrZero($row['permuta']),
            'other_sales_forms' => $getOrZero($row['otras_formas_de_ventas']),
        ]);
    }

    public function rules(): array
    {
        return [
            'rnccedula_o_pasaporte' => 'required|string|max:9|exists:clients,rnc',
            'numero_comprobante_fiscal' => 'required|string|max:19|unique:invoices,ncf',
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'rnccedula_o_pasaporte' => 'rnc',
            'numero_comprobante_fiscal' => 'ncf',
        ];
    }
}
