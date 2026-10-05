<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'sale_type',
        'unit_type',
        'price',
        'stock',
        'sku',
        'weight',
        'length',
        'width',
        'height',
        'processing_time_days',
        'status',
        'average_rating',
        'total_reviews',
        'harvest_date',
        'expiry_days',
        'expiry_date',
        'is_perishable',
        'auto_hide_expired',
        'farm_origin',
        'harvest_grade',
        'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'weight' => 'decimal:2',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'harvest_date' => 'date',
            'expiry_date' => 'date',
            'expiry_days' => 'integer',
            'is_perishable' => 'boolean',
            'auto_hide_expired' => 'boolean',
            'low_stock_threshold' => 'integer',
        ];
    }

    // Relationships

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function auction(): HasOne
    {
        return $this->hasOne(Auction::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(UserProductInteraction::class);
    }

    public function aiRecommendations(): HasMany
    {
        return $this->hasMany(AiRecommendation::class);
    }

    // Accessors

    public function getMainImageUrlAttribute(): string
    {
        $img = $this->primaryImage ?? $this->images->first();
        if ($img && !empty($img->url)) {
            return $img->url;
        }
        return asset('images/products/leather_bag_1.jpg');
    }

    public function getImageUrlsAttribute(): array
    {
        if ($this->images && $this->images->isNotEmpty()) {
            return $this->images->map(fn($img) => $img->url)->toArray();
        }
        return [$this->main_image_url];
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if ($product->is_perishable && $product->harvest_date && $product->expiry_days && empty($product->expiry_date)) {
                $product->expiry_date = \Carbon\Carbon::parse($product->harvest_date)->addDays((int) $product->expiry_days)->toDateString();
            }
        });
    }

    public function isExpired(): bool
    {
        if (!$this->is_perishable || !$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->endOfDay()->isPast();
    }

    public function isStale(): bool
    {
        return $this->isExpired();
    }

    public function isLowStock(): bool
    {
        $threshold = $this->low_stock_threshold ?? 10;
        return $this->stock <= $threshold;
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFixedPrice($query)
    {
        return $query->where('sale_type', 'fixed_price');
    }

    public function scopeAuctionType($query)
    {
        return $query->where('sale_type', 'auction');
    }

    public function scopeFresh($query)
    {
        return $query->where(function ($q) {
            $q->where('is_perishable', false)
              ->orWhereNull('expiry_date')
              ->orWhereDate('expiry_date', '>=', now()->toDateString());
        });
    }

    public function scopeStale($query)
    {
        return $query->where('is_perishable', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now()->toDateString());
    }

    public function scopePublicVisible($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->where('auto_hide_expired', false)
                  ->orWhere('is_perishable', false)
                  ->orWhereNull('expiry_date')
                  ->orWhereDate('expiry_date', '>=', now()->toDateString());
            });
    }

    public function scopeLowStock($query)
    {
        return $query->where(function ($q) {
            $q->whereColumn('stock', '<=', 'low_stock_threshold')
              ->orWhere(function ($sub) {
                  $sub->whereNull('low_stock_threshold')->where('stock', '<=', 10);
              });
        });
    }
}
