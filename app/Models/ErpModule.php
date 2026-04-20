<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ErpModule extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'icon_image', 'description', 'long_description',
        'hero_subtitle', 'hero_image',
        'features', 'youtube_videos',
        'download_url', 'download_label',
        'color', 'is_active', 'sort_order',
        'dynamic_sections',
    ];

    protected $casts = [
        'features'         => 'array',
        'youtube_videos'   => 'array',
        'dynamic_sections' => 'array',
        'is_active'        => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ErpModule $module) {
            if (empty($module->slug)) {
                $module->slug = Str::slug($module->name);
            }
        });
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_erp_module')
            ->withPivot(['activated_at', 'is_active'])
            ->withTimestamps();
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
