<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $guarded = [];

    protected $casts = ['is_featured' => 'boolean', 'is_published' => 'boolean'];

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }
}
