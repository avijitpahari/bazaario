<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'status',
    ];

    // Relationships

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Helpers

    public function isOrganic(): bool
    {
        $slug = strtolower($this->slug ?? '');
        $name = strtolower($this->name ?? '');

        return $slug === 'organic-food'
            || str_contains($slug, 'organic')
            || str_contains($name, 'organic')
            || str_contains($slug, 'produce')
            || str_contains($slug, 'vegetable')
            || str_contains($slug, 'fruit')
            || str_contains($name, 'produce')
            || str_contains($name, 'vegetable')
            || str_contains($name, 'fruit');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
