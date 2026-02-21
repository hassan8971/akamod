<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminBridge
{
    public function handle(Request $request, Closure $next): Response
    {
        // چک میکنیم آیا رمز فرستاده شده با رمز داخل فایل env یکی هست یا نه
        if ($request->header('X-Admin-Secret') !== env('ADMIN_BRIDGE_SECRET')) {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        return $next($request);
    }
}