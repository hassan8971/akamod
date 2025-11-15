@csrf
<div class="grid grid-cols-1 md:grid-cols-3 gap-6" dir="rtl">
    
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">عنوان مقاله</label>
                <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mt-6">
                <label for="content" class="block text-sm font-medium text-gray-700">محتوای مقاله</label>
                <textarea name="content" id="content-editor" rows="20">{{ old('content', $post->content) }}</textarea>
                @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">اطلاعات سئو (SEO)</h3>
            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700">متا تایتل (اختیاری)</label>
                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $post->meta_title) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="جایگزین عنوان اصلی در گوگل">
            </div>
            <div class="mt-4">
                <label for="meta_description" class="block text-sm font-medium text-gray-700">متا دسکریپشن (اختیاری)</label>
                <textarea name="meta_description" id="meta_description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="توضیح کوتاه برای نمایش در گوگل">{{ old('meta_description', $post->meta_description) }}</textarea>
            </div>
            <div class="mt-4">
                <label for="slug" class="block text-sm font-medium text-gray-700">اسلاگ (اختیاری)</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $post->slug) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="auto-generated-from-title" dir="ltr">
            </div>
        </div>
    </div>

    <div class="md:col-span-1 space-y-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">انتشار</h3>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">وضعیت</label>
                <select name="status" id="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="published" @selected(old('status', $post->status) == 'published')>منتشر شده</option>
                    <option value="draft" @selected(old('status', $post->status) == 'draft')>پیش‌نویس</option>
                </select>
            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">ذخیره</button>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <label for="blog_category_id" class="block text-sm font-medium text-gray-700">دسته‌بندی وبلاگ</label>
            <select name="blog_category_id" id="blog_category_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                <option value="">بدون دسته‌بندی</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('blog_category_id', $post->blog_category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <label for="featured_image" class="block text-sm font-medium text-gray-700">تصویر شاخص</label>
            <input type="file" name="featured_image" id="featured_image" class="mt-1 block w-full text-sm ... file:bg-blue-50 ...">
            @if($post->featured_image_path)
            <div class="mt-4">
                <img src="{{ Storage::url($post->featured_image_path) }}" alt="{{ $post->title }}" class="w-full h-auto rounded-md object-cover">
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/056tyd29jprzfk3shk5fsjjjpbt5f4t1btr26s2wq8lkj4tx/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<script>
  tinymce.init({
    selector: 'textarea#content-editor',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code preview | ltr rtl',
    directionality: 'rtl', // فعال‌سازی راست‌چین
    language: 'fa', // زبان فارسی (باید پلاگین زبان را داشته باشید یا از انگلیسی استفاده کنید)
    image_advtab: true,
    height: 600,
    // (تنظیمات آپلود تصویر در اینجا اضافه شود)
  });
</script>