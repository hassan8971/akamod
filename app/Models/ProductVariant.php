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
        'color',
        'size',
        'price',
        'discount_price',
        'buy_price',
        'stock', // How many you have'
        'buy_source',
        'boxing'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer', 
            'discount_price' => 'integer',
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
