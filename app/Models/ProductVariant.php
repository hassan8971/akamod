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
        'buy_source_id',
        'boxing',
        'sku',      // <--- اضافه شد
        'qr_code'
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

    
    // Get the buy source for this variant.
    public function buySource(): BelongsTo
    {
        return $this->belongsTo(BuySource::class);
    }

    public function colorData() 
    {
        // The second argument 'color' tells Laravel to look at the 'color' column
        return $this->belongsTo(Color::class, 'color', 'name');
    }

    protected static function booted()
    {
        static::creating(function ($variant) {
            // اگر SKU خالی بود، یک چیز رندوم بساز (اختیاری)
            if (empty($variant->sku)) {
                $variant->sku = 'SKU-' . strtoupper(uniqid()); 
            }

            // تولید QR Code موقت (فعلاً یک رشته متنی ساده)
            // بعداً می‌توانید اینجا از کتابخانه‌های تولید QR استفاده کنید
            $variant->qr_code = 'QR-' . $variant->sku . '-' . time(); 
        });
    }
}
