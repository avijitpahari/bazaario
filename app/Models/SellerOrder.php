<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'seller_id',
        'seller_order_number',
        'subtotal',
        'shipping_amount',
        'commission_rate',
        'commission_amount',
        'payout_amount',
        'status',
        'delivery_slot',
        'courier_name',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'handover_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'payout_amount' => 'decimal:2',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'handover_confirmed_at' => 'datetime',
        ];
    }

    // Relationships

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function payout(): HasOne
    {
        return $this->hasOne(Payout::class);
    }

    public function getCourierNameAttribute($value): string
    {
        return !empty($value) ? $value : 'Bazaario Hyperlocal Fleet #BLR-44';
    }

    public function getApmcCessAttribute(): float
    {
        return round(((float) $this->subtotal) * 0.015, 2);
    }

    public function getNetPayoutCalculatedAttribute(): float
    {
        return max(0, round((float) $this->subtotal - (float) $this->commission_amount - $this->apmc_cess, 2));
    }

    public function getDeliverySlotAttribute($value): ?string
    {
        if (!empty($value)) {
            return $value;
        }

        // Backward compatible fallback extraction from parent order notes
        if ($this->relationLoaded('order') || $this->order_id) {
            $notes = $this->order?->notes;
            if ($notes && preg_match('/Time Slot:\s*([^|]+)/i', $notes, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    public function scopeForSeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
