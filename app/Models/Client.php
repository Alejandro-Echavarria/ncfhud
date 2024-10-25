<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_user_id',
        'rnc',
        'business_name',
        'commercial_activity',
        'email',
    ];

    public function getCreatedAtAttribute($value)
    {
        $carbon = Carbon::parse($value)->timezone(config('app.timezone'));
        return $carbon->toFormattedDateString();
    }

    public function getUpdatedAtAttribute($value)
    {
        $carbon = Carbon::parse($value)->timezone(config('app.timezone'));
        return $carbon->toFormattedDateString();
    }

    public function scopeFilter($query, $filter)
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
}
