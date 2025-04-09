<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Filters\QueryFilter;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MannikJ\Laravel\Wallet\Traits\HasWallet;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasWallet;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'alt_phone',
        'address',
        'ver_code',
        'email_verified_at',
        'state',
        'city',
        'country',
        'bio',
        'status',
        'profile_photo',
        'farm_name',
        'is_seller',
        'is_buyer',
        'avg_delivery_rating',
        'avg_quality_rating',
        'total_reviews',
        'delivery_fee'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_seller' => 'boolean',
            'is_buyer' => 'boolean',
        ];
    }
    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
    public function sellerReviews()
    {
        return $this->hasMany(SellerReview::class, 'seller_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(SellerReview::class, 'user_id');
    }

}
