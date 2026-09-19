<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'coupon_id',
        'order_type',
        'subtotal',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'delivery_full_name',
        'delivery_phone',
        'delivery_address_line_1',
        'delivery_address_line_2',
        'delivery_city',
        'delivery_state',
        'delivery_country',
        'delivery_postal_code',
        'notes',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'placed_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function sellerOrders(): HasMany
    {
        return $this->hasMany(SellerOrder::class);
    }

    public function couponUsage(): HasOne
    {
        return $this->hasOne(CouponUsage::class);
    }

    public function winningAuction(): HasOne
    {
        return $this->hasOne(Auction::class, 'winning_order_id');
    }

    // Scopes

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
