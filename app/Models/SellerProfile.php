<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'shop_name',
        'shop_slug',
        'bio',
        'logo_path',
        'banner_path',
        'status',
        'commission_rate',
        'trust_score',
        'city',
        'state',
        'country',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'commission_rate' => 'decimal:2',
            'trust_score' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}