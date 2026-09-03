<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasTranslations;

    protected array $translatable = ['label', 'value', 'description'];

    protected $guarded = [];

    protected $casts = ['is_published' => 'boolean'];
}
