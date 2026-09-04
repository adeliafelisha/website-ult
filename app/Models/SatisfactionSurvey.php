<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SatisfactionSurvey extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quarter_1_score' => 'decimal:2',
        'quarter_2_score' => 'decimal:2',
        'quarter_3_score' => 'decimal:2',
        'quarter_4_score' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
