<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardMember extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }
}
