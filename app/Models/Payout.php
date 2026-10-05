<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'seller_order_id',
        'gross_amount',
        'commission_amount',
        'apmc_cess',
        'net_amount',
        'status',
        'payout_reference',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'apmc_cess' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    // Relationships

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(SellerOrder::class);
    }

    // Accessors

    public function getAmountAttribute(): float
    {
        return (float) $this->gross_amount;
    }

    public function getCommissionFeeAttribute(): float
    {
        return (float) $this->commission_amount;
    }

    public function getReferenceNumberAttribute(): ?string
    {
        return $this->payout_reference;
    }

    public function getApmcCessAttribute($value): float
    {
        if ($value !== null && $value > 0) {
            return (float) $value;
        }

        return round(((float) $this->gross_amount) * 0.015, 2);
    }

    // Scopes

    public function scopeForSeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
