<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Support\SafeHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasTranslations;

    protected array $translatable = ['question', 'answer', 'category', 'audience'];

    protected $guarded = [];

    protected $casts = ['is_featured' => 'boolean', 'is_published' => 'boolean'];

    public function setAnswerAttribute(?string $value): void
    {
        $this->attributes['answer'] = SafeHtml::clean($value);
    }

    public function setAnswerEnAttribute(?string $value): void
    {
        $this->attributes['answer_en'] = SafeHtml::clean($value);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }
}
