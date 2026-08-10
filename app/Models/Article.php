<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $guarded = [];

    protected $casts = ['is_featured' => 'boolean', 'is_published' => 'boolean', 'published_at' => 'datetime', 'keywords' => 'array'];

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->where(fn ($x) => $x->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
