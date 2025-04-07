<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class GeneralSetting extends Model
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($setting) {
            // Run optimize:clear when the model is updated
            Artisan::call('optimize:clear');
        });
    }

}
