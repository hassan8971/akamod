<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name', // e.g., "Small, Red" (can be auto-generated)
        'color',
        'size',
        'price',
        'stock', // How many you have'
        'boxing'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2', // Store price in cents/minor units in real life
        ];
    }

    /**
     * A variant belongs to one Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
