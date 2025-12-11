<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            
            // اطلاعات نویسنده
            'author' => [
                'id' => $this->user_id,
                'name' => $this->user ? $this->user->name : 'کاربر ناشناس',
                // اگر آواتار دارید اینجا اضافه کنید
            ],

            // تاریخ به شمسی (با استفاده از پکیج morilog/jalali که قبلا نصب کردیم)
            'created_at' => jdate($this->created_at)->format('Y/m/d'),
            
            // --- بخش مهم: پاسخ‌ها ---
            // این خط باعث می‌شود اگر پاسخی وجود داشته باشد، همین ریسورس دوباره برای آن‌ها اجرا شود (Recursion)
            'replies' => ProductReviewResource::collection($this->whenLoaded('replies')),
        ];
    }
}