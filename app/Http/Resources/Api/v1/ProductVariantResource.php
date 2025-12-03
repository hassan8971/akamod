<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Color;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // پیدا کردن اطلاعات کامل رنگ برای نمایش نام فارسی و کد هگز
        $colorObj = Color::where('name', $this->color)->first();

        return [
            'id' => $this->id,
            'product_id' => $this->product_id, // <-- اضافه شد
            'size' => $this->size,
            
            // رنگ را به صورت آبجکت برمی‌گردانیم تا شامل نام فارسی هم باشد
            'color' => [
                'name' => $this->color, // نام انگلیسی (مثلا: red)
                'persian_name' => $colorObj ? $colorObj->persian_name : null, // نام فارسی
                'hex_code' => $colorObj ? $colorObj->hex_code : null, // کد رنگ
            ],
            
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'stock' => $this->stock, // <-- اضافه شد
            
            'created_at' => $this->created_at, // <-- اضافه شد
            'updated_at' => $this->updated_at, // <-- اضافه شد
        ];
    }
}