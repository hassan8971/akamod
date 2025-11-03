<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false; // Order items don't need their own timestamps

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name', // Denormalized for easier display
        'quantity',
        'price', // Price *per item* at time of purchase
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant(): BelongsTo
    {
        // This assumes your foreign key in the 'order_items' table
        // is 'product_variant_id'
        return $this->belongsTo(ProductVariant::class);
    }
}
