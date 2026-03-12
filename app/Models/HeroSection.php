<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HeroSection extends Model
{
    protected $fillable = [
        'headline', 'subheadline',
        'cta_primary_text', 'cta_primary_url',
        'cta_secondary_text', 'cta_secondary_url',
        'background_image', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
