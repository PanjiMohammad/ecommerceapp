<?php

namespace App\Helpers;

use App\Setting;

class SettingsHelper
{
    public static function get($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getHotDealsVisibility()
    {
        return self::get('hot_deals_visibility', 'show'); // Default 'show' if not set
    }

    public static function setHotDealsVisibility($value)
    {
        return self::set('hot_deals_visibility', $value);
    }
}