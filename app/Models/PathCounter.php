<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PathCounter extends Model
{
    protected $fillable = [
        'path',
        'hit_count',
    ];
}
