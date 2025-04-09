<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'unit',
        'measurement',
        'user_id',
        'category_id',
        'thumbnail',
        'status',
    ];
    protected $casts = [
        'price' => 'double',
    ];
    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (!empty($product->slug)) {
                return;
            }
            $originalSlug = Str::slug(Str::limit($product->name, 60));
            $slug = $originalSlug;
            $count = 1;

            while (self::withTrashed()->where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-" . $count++;
            }

            $product->slug = $slug;
        });
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function relatedProducts()
    {
        return $this->hasMany(self::class, 'category_id', 'category_id')
            ->where('id', '!=', $this->id)
            ->where('status', ProductStatus::ACTIVE)
            ->latest()
            ->take(6);
    }
}
