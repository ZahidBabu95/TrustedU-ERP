<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'label', 'value', 'type', 'group'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $label = null, $type = 'text', $group = 'general')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'label' => $label ?? $key, 'type' => $type, 'group' => $group]
        );
    }
}
