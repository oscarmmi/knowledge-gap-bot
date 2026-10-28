<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocChunk extends Model
{
    protected $fillable = [
        'title',
        'body',
    ];
}
