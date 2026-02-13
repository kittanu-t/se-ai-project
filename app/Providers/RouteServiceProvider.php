<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * เส้นทางที่ระบบจะส่งผู้ใช้ไปหลังจาก login
     * (ใช้คู่กับ FortifyServiceProvider → LoginResponse override)
     */
    public const HOME = '/'; // ค่า default ไม่ใช้แล้ว แต่ Laravel บังคับให้มี

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Rate limiter พื้นฐาน (ไม่จำเป็นต้องแก้ แต่ Laravel สร้างมาให้)
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }

    /**
     * ฟังก์ชันกำหนด redirect ตาม role ของ user หลัง login
     */
    public static function redirectTo()
    {
        $user = auth()->user();

        if (!$user) {
            return '/'; // กัน fallback
        }

        if ($user->role === 'admin') {
            return route('admin.dashboard');
        }

        if ($user->role === 'staff') {
            return route('staff.bookings.index');
        }

        // default → user ปกติ
        return route('bookings.index');
    }
}