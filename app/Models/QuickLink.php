<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class QuickLink extends Model
{
    use HasTranslations;

    protected array $translatable = ['name', 'description'];

    protected $guarded = [];

    protected $casts = ['is_published' => 'boolean'];
}
