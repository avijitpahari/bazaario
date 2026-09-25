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
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function winningBid(): BelongsTo
    {
        return $this->belongsTo(AuctionBid::class, 'winning_bid_id');
    }

    public function winningOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'winning_order_id');
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

    // Helpers

    public function isLive(): bool
    {
        return in_array($this->status, ['live', 'active'])
            && now()->between($this->starts_at, $this->ends_at);
    }
}
