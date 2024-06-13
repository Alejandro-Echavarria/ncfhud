<?php

namespace App\Imports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InvoicesImport implements ToModel, WithHeadingRow
{
    private int $userId;
    private int $clientId;

    public function __construct()
    {
        $this->userId = 1;
        $this->clientId = 1;
    }

    public function model(array $row): Invoice
    {
        return new Invoice([
            'rnc' => $row['rnccedula_o_pasaporte'],
            'user_id' => 1,
            'client_id' => 1,
        ]);
    }
}
