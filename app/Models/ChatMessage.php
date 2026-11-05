<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ChatMessage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'role',
        'sender_type',
        'sender_name',
        'message',
        'attachment_path',
        'attachment_type',
        'is_read',
        'tool_name',
        'tool_data',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'tool_data' => 'array',
            'is_read' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    // Relationships

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    // Helpers

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        if (str_starts_with($this->attachment_path, 'http://') || str_starts_with($this->attachment_path, 'https://')) {
            return $this->attachment_path;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }
}
