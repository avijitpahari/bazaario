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
        'seller_type',
        'commission_rate',
        'trust_score',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'operating_radius_km',
        'operating_days',
        'verified_at',
        'gstin',
        'pan_number',
        'pan_doc_path',
        'trade_license_number',
        'bank_account_number',
        'bank_ifsc',
        'fssai_number',
        'gstin_doc_path',
        'trade_license_doc_path',
        'bank_doc_path',
        'fssai_doc_path',
        'document_statuses',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'commission_rate' => 'decimal:2',
            'trust_score' => 'decimal:2',
            'latitude' => 'float',
            'longitude' => 'float',
            'operating_radius_km' => 'integer',
            'operating_days' => 'array',
            'document_statuses' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($profile) {
            if (is_null($profile->latitude) || is_null($profile->longitude)) {
                $coords = self::getCityCoordinates($profile->city);
                if ($coords) {
                    if (is_null($profile->latitude)) {
                        $profile->latitude = $coords[0];
                    }
                    if (is_null($profile->longitude)) {
                        $profile->longitude = $coords[1];
                    }
                }
            }
        });
    }

    public static function getCityCoordinates(?string $city): array
    {
        $cityMap = [
            'kolkata'   => [22.572646, 88.363895],
            'bengaluru' => [12.971599, 77.594566],
            'bangalore' => [12.971599, 77.594566],
            'jaipur'    => [26.912434, 75.787270],
            'varanasi'  => [25.317645, 82.973915],
            'contai'    => [21.778124, 87.751624],
            'mumbai'    => [19.076090, 72.877426],
            'delhi'     => [28.704060, 77.102493],
            'digha'     => [21.626600, 87.507400],
        ];

        if (!$city) {
            return [22.572646, 88.363895]; // Default to Kolkata coordinates
        }

        $key = strtolower(trim($city));
        return $cityMap[$key] ?? [22.572646, 88.363895];
    }

    /**
     * Calculate Great Circle distance between this seller and given coordinates using Haversine formula in km.
     */
    public function distanceTo(?float $lat, ?float $lng): float
    {
        if (is_null($lat) || is_null($lng)) {
            return 0.0;
        }

        $sellerLat = $this->latitude ?? 22.572646;
        $sellerLng = $this->longitude ?? 88.363895;

        $earthRadius = 6371.0; // km
        $latFrom = deg2rad($sellerLat);
        $lonFrom = deg2rad($sellerLng);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius, 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sellerProfile()
    {
        return $this->belongsTo(self::class, 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id', 'user_id');
    }

    public function auctions()
    {
        return $this->hasMany(Auction::class, 'seller_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if seller has uploaded valid PAN details (number & document).
     */
    public function hasUploadedPan(): bool
    {
        return !empty($this->pan_number) && !empty($this->pan_doc_path);
    }

    /**
     * Check if seller has uploaded valid Bank details (account, IFSC & passbook/cheque document).
     */
    public function hasUploadedBank(): bool
    {
        return !empty($this->bank_account_number) && !empty($this->bank_ifsc) && !empty($this->bank_doc_path);
    }

    /**
     * Check if all mandatory compliance documents (Bank + PAN) are completed.
     */
    public function hasMandatoryComplianceDocuments(): bool
    {
        return $this->hasUploadedPan() && $this->hasUploadedBank();
    }

    /**
     * Calculate 2-week compliance deadline from verification date (or creation date).
     */
    public function getComplianceDeadlineAttribute(): ?\Carbon\Carbon
    {
        $baseDate = $this->verified_at ?? $this->created_at;
        return $baseDate ? $baseDate->copy()->addWeeks(2) : null;
    }

    /**
     * Days remaining until compliance grace period ends.
     */
    public function getComplianceDaysRemainingAttribute(): int
    {
        $deadline = $this->compliance_deadline;
        if (!$deadline) {
            return 14;
        }

        return max(0, (int) ceil(now()->floatDiffInDays($deadline, false)));
    }

    /**
     * Has the 2-week grace period expired without mandatory documents?
     */
    public function isComplianceGraceExpired(): bool
    {
        if ($this->hasMandatoryComplianceDocuments()) {
            return false;
        }

        $deadline = $this->compliance_deadline;
        return $deadline ? now()->greaterThan($deadline) : false;
    }
}
