<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Support\SafeHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasTranslations;

    protected array $translatable = ['title', 'category', 'excerpt', 'content', 'author', 'content_owner', 'seo_description'];

    protected $guarded = [];

    protected $casts = ['is_featured' => 'boolean', 'is_published' => 'boolean', 'published_at' => 'datetime', 'keywords' => 'array'];

    public function setContentAttribute(?string $value): void
    {
        $this->attributes['content'] = SafeHtml::clean($value);
    }

    public function setContentEnAttribute(?string $value): void
    {
        $this->attributes['content_en'] = SafeHtml::clean($value);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->where(fn ($x) => $x->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
