<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice606 extends Model
{
    use HasFactory;

    protected $table = 'invoices_606';
    protected $fillable = [
        'user_id',
        'client_id',
        'rnc',
        'business_name',
        'ncf',
        'proof_date',
        'payment_date',
        'amount',
        'itbis',
        'withheld_itbis',
    ];

    /*----------------------------------------------------------------------------*/
    // Accessors & Mutators
    /*----------------------------------------------------------------------------*/
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->timezone(config('app.timezone'))->toFormattedDateString(),
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->timezone(config('app.timezone'))->toFormattedDateString(),
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
