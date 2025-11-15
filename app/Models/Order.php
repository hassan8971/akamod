<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'shipping_address_id',
        'billing_address_id',
        'status', // e.g., 'pending', 'processing', 'shipped', 'cancelled'
        'subtotal',
        'shipping_cost',
        'shipping_method',
        'packaging_option_id', // <-- اضافه کردن
        'packaging_cost',
        'total',
        'payment_method',
        'payment_status',
        'transaction_code',
        'discount_code',
        'discount_amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address(): BelongsTo
    {
        // This assumes your foreign key in the 'orders' table
        // is 'address_id'
        return $this->belongsTo(Address::class);
    }

    public function packagingOption(): BelongsTo
    {
        return $this->belongsTo(PackagingOption::class, 'packaging_option_id');
    }
}
