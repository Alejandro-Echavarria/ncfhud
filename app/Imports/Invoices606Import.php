<?php

namespace App\Imports;

use App\Models\Invoice606;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class Invoices606Import implements ToModel, WithValidation, WithHeadingRow, WithCalculatedFormulas
{
    private int $userId;
    private int $clientId;
    private string $monthPeriod;
    private string $yearPeriod;

    public function __construct($data)
    {
        $this->userId = $data['user'];
        $this->clientId = $data['client'];
        $this->monthPeriod = $data['month'];
        $this->yearPeriod = $data['year'];
    }

    /**
     * @param array $row
     *
     * @return Model|Invoice606|null
     */
    public function model(array $row): Model|Invoice606|null
    {
        return new Invoice606([
            'user_id' => $this->userId,
            'client_id' => $this->clientId,
            'rnc' => $row['rnc_cedula'],
            'business_name' => $row['razon_social'],
            'ncf' => $row['numero_de_comprobante'],
            'proof_date' => is_string($row['fecha_comprobante'])
                ? Carbon::parse($row['fecha_comprobante'])->format('Ymd')
                : date('Ymd', ($row['fecha_comprobante'] - 25569) * 86400),
//            'proof_date' => is_string($row['fecha_comprobante']) ? $row['fecha_comprobante'] : DATE::excelToDateTimeObject($row['fecha_comprobante'])->format('Ymd'),
            'payment_date' => $this->getOrZero($row['fecha_de_pago']),
            'amount' => $this->getOrZero($row['monto_facturado']),
            'itbis' => $this->getOrZero($row['itbis_facturado']),
            'withheld_itbis' => $this->getOrZero($row['itbis_retenido']),
            'month_period' => $this->monthPeriod,
            'year_period' => $this->yearPeriod
        ]);
    }

    public function rules(): array
    {
        return [
            'fecha_comprobante' => [
                'required',
//                'regex:/^\d{4}(0[1-9]|1[0-2])([0-2][0-9]|3[0-1])$/'
            ],
            'numero_de_comprobante' => 'required|string|max:19|unique:invoices_606,ncf',
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'rnc_cedula' => 'rnc',
            'numero_de_comprobante' => 'ncf',
        ];
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
