<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Support\SafeHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasTranslations;

    protected array $translatable = ['title', 'summary', 'audience', 'requirements', 'documents', 'procedure', 'cta_label', 'location', 'service_hours', 'process_time', 'fee', 'responsible_unit', 'content_owner', 'seo_description'];

    protected $guarded = [];

    protected $casts = ['is_featured' => 'boolean', 'is_published' => 'boolean', 'published_at' => 'datetime', 'keywords' => 'array'];

    public function setProcedureAttribute(?string $value): void
    {
        $this->attributes['procedure'] = SafeHtml::clean($value);
    }

    public function setProcedureEnAttribute(?string $value): void
    {
        $this->attributes['procedure_en'] = SafeHtml::clean($value);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->where(fn ($x) => $x->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
