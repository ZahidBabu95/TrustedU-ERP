<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ErpModule extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'description',
        'features', 'color', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features'  => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ErpModule $module) {
            if (empty($module->slug)) {
                $module->slug = Str::slug($module->name);
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
