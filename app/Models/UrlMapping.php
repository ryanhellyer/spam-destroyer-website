<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlMapping extends Model
{
    protected $casts = [
        'hit_count' => 'integer',
    ];

    protected $fillable = [
        'slug',
        'url',
        'admin_hash',
        'email',
        'hit_count',
    ];
}
