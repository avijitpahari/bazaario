<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
    ];

    /**
     * Get a setting by key with an optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('setting_key', $key)->first();
        if (!$setting || $setting->setting_value === null) {
            return $default;
        }

        // Try decoding JSON if applicable
        $val = $setting->setting_value;
        $decoded = json_decode($val, true);
        return (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_bool($decoded))) ? $decoded : $val;
    }

    /**
     * Set/persist a setting key-value pair.
     */
    public static function set(string $key, mixed $value): static
    {
        $encoded = is_null($value) ? null : ((is_array($value) || is_bool($value)) ? json_encode($value) : (string)$value);
        return static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $encoded]
        );
    }

    /**
     * Get all settings as key => value dictionary.
     */
    public static function allMap(): array
    {
        return static::pluck('setting_value', 'setting_key')->toArray();
    }
}
