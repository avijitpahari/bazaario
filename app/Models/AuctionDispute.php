<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AuctionDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_number',
        'auction_id',
        'user_id',
        'seller_id',
        'winner_id',
        'dispute_type',
        'priority',
        'status',
        'subject',
        'claim_description',
        'contested_amount',
        'evidence_urls',
        'ruling',
        'ruling_notes',
        'refund_amount',
        'arbitrated_by',
        'arbitrated_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'contested_amount' => 'decimal:2',
            'refund_amount'    => 'decimal:2',
            'evidence_urls'    => 'array',
            'arbitrated_at'    => 'datetime',
            'resolved_at'      => 'datetime',
            'created_at'       => 'datetime',
            'updated_at'       => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dispute) {
            if (empty($dispute->dispute_number)) {
                $dispute->dispute_number = 'AUC-DSP-' . strtoupper(Str::random(6));
            }
        });
    }

    // Relationships

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function claimant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function arbitratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'arbitrated_by');
    }

    // Accessors & Helpers

    public function getTypeLabelAttribute(): string
    {
        return match ($this->dispute_type) {
            'quality_mismatch' => 'Catalog / Grade Mismatch',
            'non_payment'      => 'Winner Default / Non-Payment',
            'seller_refusal'   => 'Seller Refusal to Honor Lot',
            'damaged_transit'  => 'Damaged in Consignment Transit',
            'shill_bidding'    => 'Fraudulent / Shill Bidding',
            'reserve_dispute'  => 'Reserve Price Conflict',
            default            => ucfirst(str_replace('_', ' ', $this->dispute_type)),
        };
    }

    public function getRulingLabelAttribute(): string
    {
        return match ($this->ruling) {
            'refund_buyer'      => 'Full Escrow Refund to Buyer',
            'payout_seller'     => 'Release Escrow to Seller',
            'void_lot'          => 'Void Lot & Nullify Bids',
            'reassign_winner'   => 'Re-award to 2nd Highest Bidder',
            'dismissed'         => 'Claim Dismissed (Upheld Auction Outcome)',
            default             => $this->ruling ? ucfirst(str_replace('_', ' ', $this->ruling)) : 'Pending Arbitration',
        };
    }
}
