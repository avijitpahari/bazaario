<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
    ];

    protected static ?array $memoryCache = null;

    /**
     * Get a setting by key with an optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (static::$memoryCache === null) {
            try {
                static::$memoryCache = \Illuminate\Support\Facades\Cache::remember('site_settings_all', 3600, function () {
                    return static::pluck('setting_value', 'setting_key')->toArray();
                });
            } catch (\Throwable $e) {
                static::$memoryCache = static::pluck('setting_value', 'setting_key')->toArray();
            }
        }

        if (!array_key_exists($key, static::$memoryCache) || static::$memoryCache[$key] === null) {
            return $default;
        }

        $val = static::$memoryCache[$key];
        $decoded = json_decode($val, true);
        return (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_bool($decoded))) ? $decoded : $val;
    }

    /**
     * Set/persist a setting key-value pair.
     */
    public static function set(string $key, mixed $value): static
    {
        static::$memoryCache = null;
        try {
            \Illuminate\Support\Facades\Cache::forget('site_settings_all');
        } catch (\Throwable $e) {}

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

    /**
     * Get platform site name.
     */
    public static function getSiteName(string $default = 'Bazaario'): string
    {
        $val = static::get('site_name', $default);
        return (is_string($val) && trim($val) !== '') ? trim($val) : $default;
    }

    /**
     * Get platform tagline.
     */
    public static function getTagline(string $default = 'Smarter Shopping. Local Sellers. Live Auctions.'): string
    {
        $val = static::get('site_tagline', $default);
        return (is_string($val) && trim($val) !== '') ? trim($val) : $default;
    }

    /**
     * Check if a custom site logo is configured and available.
     */
    public static function hasCustomLogo(): bool
    {
        $logo = static::get('site_logo');
        if ($logo && is_string($logo) && trim($logo) !== '') {
            if (Storage::disk('public')->exists($logo) || file_exists(public_path('storage/' . $logo))) {
                return true;
            }
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://') || str_starts_with($logo, '/')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get public logo URL if custom logo exists, or fallback default image.
     */
    public static function getLogoUrl(): ?string
    {
        $logo = static::get('site_logo');
        if ($logo && is_string($logo) && trim($logo) !== '') {
            if (Storage::disk('public')->exists($logo) || file_exists(public_path('storage/' . $logo))) {
                return asset('storage/' . $logo);
            }
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://') || str_starts_with($logo, '/')) {
                return $logo;
            }
        }
        return asset('images/bazaario-logo.png');
    }

    /**
     * Check if marketplace is in single-vendor mode.
     */
    public static function isSingleVendor(): bool
    {
        return static::get('platform_mode', 'multi_vendor') === 'single_vendor';
    }

    /**
     * Check if marketplace is in multi-vendor mode.
     */
    public static function isMultiVendor(): bool
    {
        return !static::isSingleVendor();
    }

    /**
     * Get platform commission rate percentage.
     */
    public static function getCommissionRate(float $default = 8.5): float
    {
        $rate = static::get('commission_rate');
        if ($rate !== null && is_numeric($rate)) {
            return (float)$rate;
        }
        $base = static::get('platform_commission_base');
        if ($base !== null && is_numeric($base)) {
            return (float)$base;
        }
        return $default;
    }
}
