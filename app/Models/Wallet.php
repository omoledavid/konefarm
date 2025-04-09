<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends \MannikJ\Laravel\Wallet\Models\Wallet
{
    use HasFactory;

    protected $hidden = ['owner_type', 'owner_id'];
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
