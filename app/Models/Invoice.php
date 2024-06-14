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

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function itbis(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function thirdPartyItbis(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function receivedItbis(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function thirdPartyIncomeRetention(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function isr(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function selectiveTax(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function otherTaxesFees(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function legalTipAmount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function cash(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function checkTransferDeposit(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function debitCreditCard(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function creditSale(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

    protected function bondsCertificates(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->fromCents($value),
            set: fn($value) => $this->toCents($value)
        );
    }

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

    private function fromCents($value): float
    {
        return $value / 100;
    }

    private function toCents($value): int
    {
        return $value * 100;
    }
}
