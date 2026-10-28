<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GapQuery extends Model
{
    protected $fillable = [
        'question',
        'top_score',
        'answered',
        'suggested_title',
    ];

    protected function casts(): array
    {
        return [
            'top_score' => 'float',
            'answered' => 'boolean',
        ];
    }
}
