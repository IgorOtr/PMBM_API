<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = [
        'file_name',
        'file_visibility', // 1 for public, 0 for private
    ];
}
