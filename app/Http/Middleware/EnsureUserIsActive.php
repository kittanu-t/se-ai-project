<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        // ถ้ายังไม่ได้ล็อกอิน ก็ปล่อยให้ middleware 'auth' จัดการ
        if (!$request->user()) {
            return $next($request);
        }

        // ถ้าไม่ active -> เด้งออก
        if ((int) $request->user()->active !== 1) {
            // ถ้าอยากเด้ง logout ด้วย
            auth()->logout();

            // สำหรับ API/JSON ก็ส่ง 403 กลับไป
            if ($request->expectsJson()) {
                return response()->json(['message' => 'บัญชีถูกปิดการใช้งาน'], 403);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'บัญชีของคุณถูกปิดการใช้งาน',
            ]);
        }

        return $next($request);
    }
}