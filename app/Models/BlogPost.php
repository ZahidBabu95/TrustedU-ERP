<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'blog_category_id', 'author_id', 'title', 'slug',
        'excerpt', 'body', 'featured_image', 'tags',
        'status', 'published_at', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'tags'         => 'array',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (BlogPost $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('published_at', 'desc');
    }
}
