<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SiteSection extends Model
{
    protected $fillable = [
        'section_key', 'title', 'subtitle',
        'content', 'image', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function getByKey(string $key): ?static
    {
        return static::where('section_key', $key)->where('is_active', true)->first();
    }
}
