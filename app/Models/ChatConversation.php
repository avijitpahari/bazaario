<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visitor_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'title',
        'status',
        'assigned_agent_id',
        'department',
        'priority',
        'tags',
        'channel',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'last_message_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('id', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latestOfMany();
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open', 'active', 'pending']);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'active']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    // Helpers

    public function getCustomerDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->guest_name ?: 'Website Visitor (' . substr($this->visitor_id ?? 'guest', 0, 6) . ')';
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }
}

