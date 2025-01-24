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
        'amount',
        'itbis',
        'withheld_itbis',
        'month_period',
        'year_period',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rnc' => 'encrypted',
            'ncf' => 'encrypted',
        ];
    }

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
            get: fn($value) => number_format($value, 2, '.', ','),
        );
    }

    protected function itbis(): Attribute
    {
        return Attribute::make(
            get: fn($value) => number_format($value, 2, '.', ','),
        );
    }

    protected function withheldItbis(): Attribute
    {
        return Attribute::make(
            get: fn($value) => number_format($value, 2, '.', ','),
        );
    }

    protected function monthPeriod(): Attribute
    {
        return Attribute::make(
            set: fn($value) => ltrim((string)$value, '0') // Elimina ceros a la izquierda
        );
    }

    /*----------------------------------------------------------------------------*/
    // Scopes (local)
    /*----------------------------------------------------------------------------*/
    public function scopeFilter($query, $clientFilter, $monthFilter, $yearFilter): void
    {
        $query->when($clientFilter ?? null && $monthFilter && null && $yearFilter ?? null, function ($query, $clientFilter) use ($monthFilter, $yearFilter) {
            $query->where('client_id', "$clientFilter")
                ->where('month_period', "$monthFilter")
                ->where('year_period', "$yearFilter");
        });
    }
}
