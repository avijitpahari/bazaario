<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustScoreLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'seller_id',
        'fulfillment_rate',
        'average_rating',
        'verification_bonus',
        'computed_score',
    ];

    protected function casts(): array
    {
        return [
            'fulfillment_rate' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'verification_bonus' => 'decimal:2',
            'computed_score' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    // Relationships

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
