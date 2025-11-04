<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVideo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'path',
        'alt_text',
        'order',
    ];

    /**
     * Get the product that owns the video.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}


