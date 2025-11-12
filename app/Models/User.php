<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'zip_code',
        'province',
        'city',
        'address',
        'password',
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
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * A user can have many saved addresses.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get all reviews written by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Check if the user has purchased a specific product.
     * (بررسی می‌کند آیا کاربر این محصول را خریده است یا نه)
     */
    public function hasPurchased(int $productId): bool
    {
        // 1. Get all product variant IDs for the given product
        $variantIds = Product::find($productId)->variants->pluck('id');

        // 2. Check if this user has any 'delivered' or 'shipped' orders
        // that contain any of those variant IDs.
        return $this->orders()
            ->whereIn('status', ['delivered', 'shipped', 'processing']) // یا هر وضعیتی که نشانه خرید قطعی است
            ->whereHas('items', function ($query) use ($variantIds) {
                $query->whereIn('product_variant_id', $variantIds);
            })
            ->exists();
    }

}
