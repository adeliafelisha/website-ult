<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $guarded = [];

    protected $casts = ['is_featured' => 'boolean', 'is_published' => 'boolean', 'published_at' => 'datetime', 'keywords' => 'array'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->where(fn ($x) => $x->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
