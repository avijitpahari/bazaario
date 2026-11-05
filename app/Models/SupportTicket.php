<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'seller_id',
        'order_id',
        'order_item_id',
        'category',
        'subject',
        'priority',
        'status',
        'is_escalated',
        'escalated_at',
        'escalation_reason',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_escalated' => 'boolean',
            'escalated_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')->orderBy('id', 'asc');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(SupportTicketMessage::class, 'ticket_id')->latestOfMany();
    }

    // Static Helpers

    public static function generateTicketNumber(): string
    {
        do {
            $number = 'TIC-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (static::where('ticket_number', $number)->exists());

        return $number;
    }

    // Accessors & Presenters

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'order_issue' => 'Order & Delivery Issue',
            'return_refund' => 'Return / Refund Request',
            'delivery_delay' => 'Shipping & Delivery Delay',
            'auction' => 'Live Auction Inquiry',
            'billing' => 'Payment & Billing Question',
            default => 'General Customer Inquiry',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-amber-100 text-amber-800 border-amber-300',
            'in_progress' => 'bg-blue-100 text-blue-800 border-blue-300',
            'waiting_customer' => 'bg-purple-100 text-purple-800 border-purple-300',
            'waiting_seller' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'escalated' => 'bg-rose-100 text-rose-800 border-rose-300 animate-pulse',
            'resolved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'closed' => 'bg-slate-100 text-slate-700 border-slate-300',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'text-rose-600 bg-rose-50 border-rose-200',
            'high' => 'text-amber-600 bg-amber-50 border-amber-200',
            'low' => 'text-slate-600 bg-slate-50 border-slate-200',
            default => 'text-blue-600 bg-blue-50 border-blue-200', // medium
        };
    }
}
