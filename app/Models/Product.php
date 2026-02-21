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
        'care_and_maintenance',
        'invoice_number',
        'is_visible',
        'is_for_men',
        'is_for_women',
        'is_for_kids',
        'admin_id',
        'hover_image_id',
        'apple_title',
        'apple_description',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'is_for_men' => 'boolean',
            'is_for_women' => 'boolean',
            'is_for_kids' => 'boolean',
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

    /**
     * Get all reviews for this product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get only the approved reviews, sorted by newest.
     * (فقط نظرات تایید شده را برمی‌گرداند)
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()
                    ->where('is_approved', true)
                    ->whereNull('parent_id'); // <-- فقط نظراتی که والد ندارند
    }

    /**
     * Get the average rating for this product.
     * (میانگین امتیازات را محاسبه می‌کند)
     */
    public function averageRating(): float
    {
        // avg() will return 0 if there are no reviews
        return (float) $this->approvedReviews()->avg('rating');
    }

    /**
     * Get the count of approved ratings.
     * (تعداد کل نظرات تایید شده را برمی‌گرداند)
     */
    public function approvedReviewsCount(): int
    {
        return $this->approvedReviews()->count();
    }

    public function hoverImage()
    {
        return $this->belongsTo(ProductImage::class, 'hover_image_id');
    }
}
