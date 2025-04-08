<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slug',
        'image',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
