<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'seller_id',
        'starting_price',
        'reserve_price',
        'current_price',
        'minimum_increment',
        'starts_at',
        'ends_at',
        'status',
        'winner_id',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
            'reserve_price' => 'decimal:2',
            'current_price' => 'decimal:2',
            'minimum_increment' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    // Relationships

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['live', 'active']);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Helpers

    public function isLive(): bool
    {
        return in_array($this->status, ['live', 'active'])
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function isReserveMet(): bool
    {
        if (is_null($this->reserve_price) || (float) $this->reserve_price <= 0) {
            return true;
        }

        $bidCount = $this->relationLoaded('bids') ? $this->bids->count() : $this->bids()->count();
        if ($bidCount === 0) {
            return false;
        }

        $highestBid = $this->relationLoaded('bids')
            ? (float) ($this->bids->max('amount') ?? 0)
            : (float) ($this->bids()->max('amount') ?? 0);

        if ($highestBid <= 0) {
            $highestBid = (float) $this->current_price;
        }

        return $highestBid >= (float) $this->reserve_price;
    }

    public function canBeCancelled(): bool
    {
        if (in_array($this->status, ['ended', 'cancelled'])) {
            return false;
        }

        $bidCount = $this->relationLoaded('bids') ? $this->bids->count() : $this->bids()->count();
        return $bidCount === 0;
    }
}
