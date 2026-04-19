<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'message' => 'required|string|max:2000',
        ], [
            'name.required'    => 'لطفاً نام خود را وارد کنید.',
            'phone.required'   => 'لطفاً شماره تماس خود را وارد کنید.',
            'message.required' => 'لطفاً متن پیام خود را بنویسید.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        ContactMessage::create([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'پیام شما با موفقیت ارسال شد. به زودی با شما تماس می‌گیریم.'
        ]);
    }
}