<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\ConcatenatesClientDetails;
use Illuminate\Support\Collection;

class Client extends Model
{
    use HasFactory, ConcatenatesClientDetails;

    protected $fillable = [
        'created_by_user_id',
        'rnc',
        'business_name',
        'commercial_activity',
        'email',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /*----------------------------------------------------------------------------*/
    // Relations
    /*----------------------------------------------------------------------------*/
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
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

    /*----------------------------------------------------------------------------*/
    // Scopes (local)
    /*----------------------------------------------------------------------------*/
    public function scopeFilter($query, $filter): void
    {
        $query->when($filter ?? null, function ($query, $search) {
            $query->whereAny([
                'rnc',
                'business_name',
                'commercial_activity',
                'email',
                'created_at'
            ], config('app.db_driver') === 'pgsql' ? 'ILIKE' : 'LIKE', "%$search%");
        });
    }

    /*----------------------------------------------------------------------------*/
    // My functions
    /*----------------------------------------------------------------------------*/

    /**
     * Obtener los detalles combinados de todos los clientes.
     *
     * @return Collection
     */
    public static function getAllCombinedDetails(): Collection
    {
        return self::all()->map(function ($client) {
            return $client->getCombinedDetails();
        });
    }
}
