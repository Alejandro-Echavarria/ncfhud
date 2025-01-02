<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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

    /*----------------------------------------------------------------------------*/
    // Relations
    /*----------------------------------------------------------------------------*/
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /*----------------------------------------------------------------------------*/
    // Accessors & Mutators
    /*----------------------------------------------------------------------------*/
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('d/m/Y'),
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('d/m/Y'),
        );
    }

    protected function proofDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('d/m/Y'),
        );
    }

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => "RD$ " . number_format($value, 2, '.', ','),
        );
    }

    protected function itbis(): Attribute
    {
        return Attribute::make(
            get: fn($value) => "RD$ " . number_format($value, 2, '.', ','),
        );
    }


    /*----------------------------------------------------------------------------*/
    // Scopes (local)
    /*----------------------------------------------------------------------------*/
    public function scopeFilter($query, $clientFilter, $monthFilter, $yearFilter)
    {
        $query->when($clientFilter ?? null && $monthFilter && null && $yearFilter ?? null, function ($query, $clientFilter) use ($monthFilter, $yearFilter) {
            $query->where('client_id', "$clientFilter")
                ->whereMonth(
                    config('app.db_driver') === 'pgsql' ?
                        DB::raw("TO_DATE(proof_date, 'YYYYMMDD')") :
                        DB::raw("STR_TO_DATE(proof_date, '%Y%m%d')"), "$monthFilter"
                )
                ->whereYear(
                    config('app.db_driver') === 'pgsql' ?
                        DB::raw("TO_DATE(proof_date, 'YYYYMMDD')") :
                        DB::raw("STR_TO_DATE(proof_date, '%Y%m%d')"), "$yearFilter"
                );
        });
    }
}
