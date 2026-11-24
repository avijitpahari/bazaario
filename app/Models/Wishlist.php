<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sharing_token',
        'is_public',
        'title',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Get or generate a unique public sharing token.
     */
    public function getOrCreateSharingToken(): string
    {
        if (!$this->sharing_token) {
            $this->sharing_token = 'WSH-' . strtoupper(Str::random(12));
            $this->is_public = true;
            $this->save();
        }

        return $this->sharing_token;
    }

    /**
     * Merge guest session wishlist items into user's database wishlist.
     */
    public static function mergeSessionWishlist(User $user): void
    {
        $sessionWishlist = session('wishlist', []);
        if (empty($sessionWishlist) || !is_array($sessionWishlist)) {
            return;
        }

        $wishlist = $user->wishlist()->firstOrCreate(['user_id' => $user->id]);

        foreach ($sessionWishlist as $productId) {
            if (Product::where('id', $productId)->exists()) {
                $wishlist->items()->firstOrCreate([
                    'product_id' => $productId,
                ]);
            }
        }

        session()->forget('wishlist');
    }
}
