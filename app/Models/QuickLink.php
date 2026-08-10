<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickLink extends Model
{
    protected $guarded = [];

    protected $casts = ['is_published' => 'boolean'];
}
