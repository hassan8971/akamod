<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:newsletter_subscribers,email'
        ], [
            'name.required'  => 'لطفاً نام خود را وارد کنید.',
            'email.required' => 'لطفاً ایمیل خود را وارد کنید.',
            'email.email'    => 'فرمت ایمیل نامعتبر است.',
            'email.unique'   => 'این ایمیل قبلاً در خبرنامه ثبت شده است.'
        ]);

        if ($validator->fails()) {
            // Get the first error message (whether it's name or email)
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        NewsletterSubscriber::create([
            'name'  => $request->name,
            'email' => $request->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'با موفقیت ثبت شد! ممنون از همراهی شما.'
        ]);
    }
}