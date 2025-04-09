<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = ['user_id', 'reference', 'total_amount', 'status', 'callback_url', 'user_address_id', 'delivery_fee'];
    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }
    public function items() {
        return $this->hasMany(OrderItem::class);
    }
    public function buyerAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }
}
