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
        $this->userId = $data['user'];
        $this->clientId = $data['client'];
    }

    public function model(array $row): Invoice
    {
        return new Invoice([
            'user_id' => (int)$this->userId,
            'client_id' => (int)$this->clientId,

            'rnc' => (string)$row['rnccedula_o_pasaporte'],
            'identification_type' => (int)$row['tipo_identificacion'],
            'ncf' => (string)$row['numero_comprobante_fiscal'],
            'ncf_modified' => (string)$row['numero_comprobante_fiscal_modificado'],
            'income_type' => (string)$row['tipo_de_ingreso'],
            'proof_date' => (string)$row['fecha_comprobante'],
            'withholding_date' => (string)$row['fecha_de_retencion'],
            'amount' => $this->getOrZero($row['monto_facturado']),
            'itbis' => $this->getOrZero($row['itbis_facturado']),
            'third_party_itbis_withheld' => $this->getOrZero($row['itbis_retenido_por_terceros']),
            'received_itbis' => $this->getOrZero($row['itbis_percibido']),
            'third_party_income_retention' => $this->getOrZero($row['retencion_renta_por_terceros']),
            'isr' => $this->getOrZero($row['isr_percibido']),
            'selective_tax' => $this->getOrZero($row['impuesto_selectivo_al_consumo']),
            'other_taxes_fees' => $this->getOrZero($row['otros_impuestostasas']),
            'legal_tip_amount' => $this->getOrZero($row['monto_propina_legal']),
            'cash' => $this->getOrZero($row['efectivo']),
            'check_transfer_deposit' => $this->getOrZero($row['cheque_transferencia_deposito']),
            'debit_credit_card' => $this->getOrZero($row['tarjeta_debitocredito']),
            'credit_sale' => $this->getOrZero($row['venta_a_credito']),
            'bonds_certificates' => $this->getOrZero($row['bonos_o_certificados_de_regalo']),
            'barter' => $this->getOrZero($row['permuta']),
            'other_sales_forms' => $this->getOrZero($row['otras_formas_de_ventas']),
        ]);
    }

    public function rules(): array
    {
        return [
            'fecha_comprobante' => [
                'required',
                'regex:/^\d{4}(0[1-9]|1[0-2])([0-2][0-9]|3[0-1])$/'
            ],
            'numero_comprobante_fiscal' => 'required|string|max:19|unique:invoices,ncf',
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'rnccedula_o_pasaporte' => 'rnc',
            'numero_comprobante_fiscal' => 'ncf',
            'rnc' => 'rnc, cedula o pasaporte',
            'identification_type' => 'tipo identificacion',
            'ncf' => 'ncf',
            'ncf_modified' => 'ncf modificado',
            'income_type' => 'tipo de ingreso',
            'proof_date' => 'fecha de comprobante',
            'withholding_date' => 'fecha de retencion',
            'amount' => 'monto facturado',
            'itbis' => 'itbis facturado',
            'third_party_itbis_withheld' => 'itbis retenido por terceros',
            'received_itbis' => 'itbis percibido',
            'third_party_income_retention' => 'retencion renta por terceros',
            'isr' => 'isr percibido',
            'selective_tax' => 'impuesto selectivo al consumo',
            'other_taxes_fees' => 'otros impuestos/tasas',
            'legal_tip_amount' => 'monto propina legal',
            'cash' => 'efectivo',
            'check_transfer_deposit' => 'cheque, transferencia, deposito',
            'debit_credit_card' => 'tarjeta debito/crédito',
            'credit_sale' => 'venta a crédito',
            'bonds_certificates' => 'bonos o certificados de regalo',
            'barter' => 'permuta',
            'other_sales_forms' => 'otras formas de ventas',
        ];
    }

    public function headingRow(): int
    {
        return 11;
    }

    /*----------------------------------------------------------------------------*/
    // My functions
    /*----------------------------------------------------------------------------*/

    /**
     * @param float|null $value
     * @return float
     */
    public function getOrZero(?float $value): float
    {
        if (!is_numeric($value)) return 0;

        return round((float)$value, 2);
    }
}
