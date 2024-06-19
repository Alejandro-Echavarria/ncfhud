<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'rnc',
        'user_id',
        'client_id',
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
    ];

    public function getCreatedAtAttribute($value): string
    {
        $carbon = Carbon::parse($value)->timezone(config('app.timezone'));
        return $carbon->toFormattedDateString();
    }

    public function getUpdatedAtAttribute($value): string
    {
        $carbon = Carbon::parse($value)->timezone(config('app.timezone'));
        return $carbon->toFormattedDateString();
    }
}
