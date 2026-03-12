<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    protected static function booted(): void
    {
        static::creating(function (BlogCategory $cat) {
            if (empty($cat->slug)) {
                $cat->slug = Str::slug($cat->name);
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }
}
