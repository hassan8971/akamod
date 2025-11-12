{{-- 
  This is a recursive partial view.
  $review: The current comment or reply.
  $product: The product being viewed.
  $hasPurchased: If the logged-in user has purchased this item.
--}}
<div class="border-b border-gray-200 pb-6" x-data="{ showReplyForm: false }">
    <div class="flex items-center mb-2">
        <span class="font-semibold text-gray-800">{{ $review->user->name ?? 'کاربر' }}</span>
        <span class="text-gray-400 mx-2">|</span>
        <span class="text-sm text-gray-500">{{ $review->created_at->format('Y/m/d') }}</span>
        @if($review->parent_id)
            <span class="text-xs text-blue-600 mr-2">(در پاسخ به {{ $review->parent->user->name ?? 'نظر قبلی' }})</span>
        @endif
    </div>
    
    @if ($review->rating)
        <div class="flex items-center mb-3">
            @for ($i = 1; $i <= 5; $i++)
                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.448a1 1 0 00-1.175 0l-3.368 2.448c-.784.57-1.838-.197-1.54-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.24 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z" />
                </svg>
            @endfor
        </div>
    @endif
    
    <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>

    @if($hasPurchased)
        <button @click="showReplyForm = !showReplyForm" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
            پاسخ دادن
        </button>
    @endif

    <div x-show="showReplyForm" x-transition class="mt-4 mr-8 border-r-2 border-gray-200 pr-4">
        <form action="{{ route('products.reviews.store', $product) }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $review->id }}">
            <div>
                <label for="comment_{{ $review->id }}" class="block text-sm font-medium text-gray-700">پاسخ شما</label>
                <textarea name="comment" id="comment_{{ $review->id }}" rows="3" 
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" 
                          placeholder="پاسخ خود را بنویسید..." required minlength="3"></textarea>
            </div>
            <div class="flex justify-end items-center mt-2 space-x-2 space-x-reverse">
                <button type="button" @click="showReplyForm = false" class="text-sm text-gray-600">انصراف</button>
                <button type="submit" class="px-4 py-1 bg-blue-600 text-white rounded-lg text-sm">ارسال پاسخ</button>
            </div>
        </form>
    </div>

    <div class="mt-6 mr-8 border-r-2 border-gray-200 pr-4 space-y-6">
        @foreach ($review->replies as $reply)
            @include('shop.partials._review_comment', ['review' => $reply, 'product' => $product, 'hasPurchased' => $hasPurchased])
        @endforeach
    </div>
</div>