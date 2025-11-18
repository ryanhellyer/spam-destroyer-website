<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathCounter extends Model
{
    /** Enables factory support for tests (PathCounter::factory()) */
    use HasFactory;

    protected $fillable = [
        'path',
        'hit_count',
    ];
}
