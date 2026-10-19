<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }
}
