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
        'aadhaar_number',
        'aadhaar_doc_path',
        'mandi_license_number',
        'mandi_license_doc_path',
        'udyam_number',
        'udyam_doc_path',
        'document_statuses',
        'provided_documents',
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
            'provided_documents' => 'array',
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

    /**
     * Get the effective commission rate for this seller.
     * Uses the seller's custom override if configured by admin, otherwise falls back to the dynamic platform rate.
     */
    public function getEffectiveCommissionRate(): float
    {
        if ($this->commission_rate !== null && is_numeric($this->commission_rate)) {
            return (float) $this->commission_rate;
        }

        return SiteSetting::getCommissionRate(10.0);
    }

    /**
     * Compute and update seller trust score based on verification and fulfillment performance.
     */
    public function recalculateTrustScore(): float
    {
        $score = 75.00;

        if ($this->status === 'approved') {
            $score += 15.00;
        }

        if ($this->verified_at !== null) {
            $score += 3.00;
        }

        if (!empty($this->bank_account_number)) {
            $score += 2.00;
        }

        // Fulfillment performance
        $totalOrders = SellerOrder::where('seller_id', $this->user_id)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        $completedOrders = SellerOrder::where('seller_id', $this->user_id)
            ->whereIn('status', ['delivered', 'fulfilled'])
            ->count();

        if ($totalOrders > 0) {
            $fulfillmentRate = ($completedOrders / $totalOrders) * 100;
            $score += round(($fulfillmentRate / 100) * 5, 2);
        }

        // Penalty for disputes or returns
        $returnsCount = 0;
        try {
            $returnsCount = OrderReturn::whereHas('orderItem.sellerOrder', function ($q) {
                $q->where('seller_id', $this->user_id);
            })->whereNotIn('status', ['rejected'])->count();
        } catch (\Throwable $e) {}

        $score -= ($returnsCount * 3.00);

        $finalScore = max(10.00, min(100.00, round($score, 2)));

        $this->update(['trust_score' => $finalScore]);

        try {
            TrustScoreLog::create([
                'seller_id'          => $this->user_id,
                'fulfillment_rate'   => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 100.00,
                'average_rating'     => 5.00,
                'verification_bonus' => $this->status === 'approved' ? 15.00 : 0.00,
                'computed_score'     => $finalScore,
            ]);
        } catch (\Throwable $e) {}

        return $finalScore;
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
     * Get a rich, structured collection of all documents provided by this merchant.
     * Evaluates both uploaded files and entered identification numbers across all document types.
     *
     * @param bool $allSupported If true, returns all supported document definitions with their current state.
     *                            If false (default), returns only documents that have been provided (file or number).
     */
    public function getProvidedDocumentsList(bool $allSupported = false): array
    {
        $definitions = [
            'pan' => [
                'key'             => 'pan',
                'label'           => 'Permanent Account Number (PAN)',
                'short_label'     => 'PAN Card',
                'badge'           => 'Tax Compliance',
                'badge_color'     => 'amber',
                'icon'            => 'badge',
                'number'          => $this->pan_number,
                'secondary'       => null,
                'number_label'    => 'PAN No',
                'file_path'       => $this->pan_doc_path,
                'is_mandatory'    => true,
                'description'     => 'Individual proprietor or corporate PAN card proof',
            ],
            'bank' => [
                'key'             => 'bank',
                'label'           => 'Escrow Bank Account & Proof',
                'short_label'     => 'Bank Account',
                'badge'           => 'Escrow Payouts',
                'badge_color'     => 'emerald',
                'icon'            => 'account_balance',
                'number'          => $this->bank_account_number,
                'secondary'       => $this->bank_ifsc ? "IFSC: {$this->bank_ifsc}" : null,
                'number_label'    => 'A/C No',
                'file_path'       => $this->bank_doc_path,
                'is_mandatory'    => true,
                'description'     => 'Cancelled bank cheque leaf or first page of bank passbook / statement',
            ],
            'gstin' => [
                'key'             => 'gstin',
                'label'           => 'GST Registration Certificate',
                'short_label'     => 'GSTIN Certificate',
                'badge'           => 'Indirect Tax',
                'badge_color'     => 'blue',
                'icon'            => 'receipt_long',
                'number'          => $this->gstin,
                'secondary'       => null,
                'number_label'    => 'GSTIN',
                'file_path'       => $this->gstin_doc_path,
                'is_mandatory'    => false,
                'description'     => 'GST registration certificate with active tax identification number',
            ],
            'trade_license' => [
                'key'             => 'trade_license',
                'label'           => 'Municipality Trade License',
                'short_label'     => 'Trade License',
                'badge'           => 'Municipal Body',
                'badge_color'     => 'purple',
                'icon'            => 'domain',
                'number'          => $this->trade_license_number,
                'secondary'       => null,
                'number_label'    => 'License No',
                'file_path'       => $this->trade_license_doc_path,
                'is_mandatory'    => false,
                'description'     => 'Local municipal corporation or Panchayat trade / establishment license',
            ],
            'fssai' => [
                'key'             => 'fssai',
                'label'           => 'FSSAI Food Safety License',
                'short_label'     => 'FSSAI Certificate',
                'badge'           => 'Food & Agro',
                'badge_color'     => 'green',
                'icon'            => 'restaurant',
                'number'          => $this->fssai_number,
                'secondary'       => null,
                'number_label'    => 'FSSAI No',
                'file_path'       => $this->fssai_doc_path,
                'is_mandatory'    => false,
                'description'     => 'Food Safety and Standards Authority of India manufacturing / retailing license',
            ],
            'aadhaar' => [
                'key'             => 'aadhaar',
                'label'           => 'Aadhaar / Government Photo ID',
                'short_label'     => 'Aadhaar / Gov ID',
                'badge'           => 'Proprietor Identity',
                'badge_color'     => 'indigo',
                'icon'            => 'fingerprint',
                'number'          => $this->aadhaar_number,
                'secondary'       => null,
                'number_label'    => 'ID No',
                'file_path'       => $this->aadhaar_doc_path,
                'is_mandatory'    => false,
                'description'     => 'Government-issued photo identification proof (Aadhaar, Voter ID, or Passport)',
            ],
            'mandi_license' => [
                'key'             => 'mandi_license',
                'label'           => 'APMC / Mandi Market Trading License',
                'short_label'     => 'Mandi License',
                'badge'           => 'Wholesale & Auctions',
                'badge_color'     => 'amber',
                'icon'            => 'agriculture',
                'number'          => $this->mandi_license_number,
                'secondary'       => null,
                'number_label'    => 'APMC No',
                'file_path'       => $this->mandi_license_doc_path,
                'is_mandatory'    => false,
                'description'     => 'APMC market yard trading permit for agricultural auctions and wholesale consignments',
            ],
            'udyam' => [
                'key'             => 'udyam',
                'label'           => 'Udyam / MSME Registration',
                'short_label'     => 'MSME Certificate',
                'badge'           => 'Small Enterprise',
                'badge_color'     => 'slate',
                'icon'            => 'workspace_premium',
                'number'          => $this->udyam_number,
                'secondary'       => null,
                'number_label'    => 'Udyam No',
                'file_path'       => $this->udyam_doc_path,
                'is_mandatory'    => false,
                'description'     => 'Ministry of Micro, Small and Medium Enterprises (MSME) registration certificate',
            ],
        ];

        $results = [];
        $explicitProvided = is_array($this->provided_documents) ? $this->provided_documents : [];

        foreach ($definitions as $key => $doc) {
            $hasFile = !empty($doc['file_path']);
            $hasNumber = !empty($doc['number']) || ($key === 'bank' && !empty($this->bank_ifsc));
            $isExplicit = in_array($key, $explicitProvided);

            $doc['has_file'] = $hasFile;
            $doc['has_number'] = $hasNumber;
            $doc['is_provided'] = $hasFile || $hasNumber || $isExplicit;
            $doc['is_pdf'] = $hasFile && str_ends_with(strtolower($doc['file_path']), '.pdf');

            // Determine status
            if ($hasFile && $hasNumber) {
                $doc['status'] = 'complete';
                $doc['status_label'] = 'File Attached & Details Verified';
                $doc['status_color'] = 'emerald';
            } elseif ($hasFile) {
                $doc['status'] = 'file_only';
                $doc['status_label'] = 'Proof File Attached';
                $doc['status_color'] = 'emerald';
            } elseif ($hasNumber) {
                $doc['status'] = 'details_only';
                $doc['status_label'] = 'Details Provided — Awaiting File';
                $doc['status_color'] = 'amber';
            } else {
                $doc['status'] = 'pending';
                $doc['status_label'] = 'Not Provided';
                $doc['status_color'] = 'slate';
            }

            if ($allSupported || $doc['is_provided']) {
                $results[$key] = $doc;
            }
        }

        return $results;
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
