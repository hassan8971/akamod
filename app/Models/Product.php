<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'product_id', // Your internal SKU
        'invoice_number',
        'is_visible',
        'is_for_men',
        'is_for_women',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'is_for_men' => 'boolean',
            'is_for_women' => 'boolean',
        ];
    }

    /**
     * A product belongs to one Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A product has many buyable Variants.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * A product can have many images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'product_video_pivot', 'product_id', 'video_id');
    }
    public function packagingOptions()
    {
        return $this->belongsToMany(PackagingOption::class, 'product_packaging_option_pivot');
    }
    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,          // The model we are linking to
            'related_products_pivot',      // The pivot table name
            'product_id',            // Foreign key on pivot for this model
            'related_product_id'     // Foreign key on pivot for the linked model
        );
    }

    //  Get the admin who created this product.
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
