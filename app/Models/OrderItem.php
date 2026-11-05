<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'seller_order_id',
        'product_id',
        'product_name',
        'product_image',
        'sku',
        'unit_price',
        'quantity',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    // Accessors

    public function getImageUrlAttribute(): string
    {
        $path = $this->product_image;

        if (empty($path)) {
            if ($this->product) {
                return $this->product->main_image_url;
            }
            return asset('images/products/keyboard_1.jpg');
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['storage/', 'images/']) || file_exists(public_path($path))) {
            return asset($path);
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }

    // Relationships

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(SellerOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
