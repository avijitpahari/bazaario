<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminFeed extends Model
{
    use HasFactory;

    protected $table = 'admin_feeds';

    protected $fillable = [
        'type',
        'title',
        'description',
        'icon',
        'badge_text',
        'badge_color',
        'action_url',
        'metadata',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_read' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Record a new real-time administrative feed notification.
     */
    public static function record(
        string $title,
        string $description = '',
        string $type = 'system',
        string $icon = 'notifications',
        string $badgeText = 'Update',
        string $badgeColor = 'blue',
        ?string $actionUrl = null,
        array $metadata = []
    ): self {
        return self::create([
            'title'       => $title,
            'description' => $description,
            'type'        => $type,
            'icon'        => $icon,
            'badge_text'  => $badgeText,
            'badge_color' => $badgeColor,
            'action_url'  => $actionUrl,
            'metadata'    => $metadata,
            'is_read'     => false,
        ]);
    }
}
