<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    // Accessors

    public function getUrlAttribute(): string
    {
        $path = $this->image_path;
        if (empty($path)) {
            return asset('images/products/leather_bag_1.jpg');
        }
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        if (\Illuminate\Support\Str::startsWith($path, 'storage/')) {
            return asset($path);
        }
        return \Illuminate\Support\Facades\Storage::url($path);
    }

    // Relationships

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
