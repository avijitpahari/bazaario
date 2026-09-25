<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'full_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'landmark',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'latitude'   => 'decimal:7',
            'longitude'  => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'work'  => 'apartment',
            'other' => 'location_on',
            default => 'home',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'work'  => 'Work',
            'other' => 'Other',
            default => 'Home',
        };
    }
}
