<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasTranslations;

    protected array $translatable = ['name', 'description'];

    protected $guarded = [];

    protected $casts = ['is_featured' => 'boolean'];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
