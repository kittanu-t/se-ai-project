<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FieldPublicController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnnouncementPublicController;

use App\Http\Controllers\Staff\BookingController as StaffBookingController;
use App\Http\Controllers\Staff\FieldController   as StaffFieldController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController        as AdminUserController;
use App\Http\Controllers\Admin\SportsFieldController as AdminFieldController; 
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\BookingController     as AdminBookingController;
use App\Http\Controllers\Admin\FieldUnitController; 

use App\Models\Announcement;
use App\Models\SportsField;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

// Home: ประกาศล่าสุด
Route::get('/', function () {
    $ann = Announcement::whereNotNull('published_at')
        ->orderByDesc('published_at')
        ->limit(5)
        ->get();
    return view('welcome', ['announcements' => $ann]);
})->name('home');

// รายการสนามใหญ่ (public)
Route::get('/fields', [FieldPublicController::class, 'index'])->name('fields.index');
// หน้าสนามใหญ่ + เลือกคอร์ต + ปฏิทิน
Route::get('/fields/{field}', [FieldPublicController::class, 'show'])->name('fields.show');

// JSON: รายการคอร์ตของสนาม (ใช้ตอนหน้า Create Booking)
Route::get('/api/fields/{field}/units', function (SportsField $field) {
    return $field->units()->orderBy('index')->get(['id','name','status']);
})->name('fields.units.list');

// JSON: events เฉพาะคอร์ต
Route::get('/api/fields/{field}/units/{unit}/events', [FieldPublicController::class, 'unitEvents'])
    ->name('fields.units.events');


Route::middleware(['auth', 'active', 'verified'])->group(function () {

    // Notifications
    Route::get('/notifications',            [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',  [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/notifications/feed',       [NotificationController::class, 'feed'])->name('notifications.feed');

    // Announcements (ผู้ใช้)
    Route::get('/announcements',                [AnnouncementPublicController::class, 'index'])->name('user.announcements.index');
    Route::get('/announcements/{announcement}', [AnnouncementPublicController::class, 'show'])->name('user.announcements.show');

    // USER
    Route::middleware('user')->group(function () {
        Route::get('/bookings',         [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create',  [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings',        [BookingController::class, 'store'])->name('bookings.store');
        Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');

        // Account
        Route::get('/account',          [AccountController::class, 'show'])->name('account.show');
        Route::put('/account',          [AccountController::class, 'update'])->name('account.update');
        Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');
    });

    // STAFF
    Route::prefix('staff')->name('staff.')->middleware('staff')->group(function () {
        // คิวคำขอจอง
        Route::get('/bookings',               [StaffBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{id}/approve', [StaffBookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{id}/reject',  [StaffBookingController::class, 'reject'])->name('bookings.reject');

        // สนามที่รับผิดชอบ + ปิด/เปิด
        Route::get('/fields', [StaffFieldController::class, 'myFields'])->name('fields.index');

        // field management (ปิด/เปิดสนาม, ปิด/เปิดคอร์ต)
        Route::post('/fields/{field}/close',     [StaffFieldController::class, 'closeField'])->name('fields.close');
        Route::post('/fields/{field}/open',      [StaffFieldController::class, 'openField'])->name('fields.open');
        Route::post('/fields/{field}/units/{unit}/close', [StaffFieldController::class, 'closeUnit'])->name('units.close');
        Route::post('/fields/{field}/units/{unit}/open',  [StaffFieldController::class, 'openUnit'])->name('units.open');

        // ปฏิทินการใช้งานสนาม (ของสนามที่รับผิดชอบ)
        Route::get('/fields/schedule', [StaffFieldController::class, 'schedule'])->name('fields.schedule');
    });

    // ADMIN
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

        // Dashboard + management หลัก
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('/users',        AdminUserController::class);
        Route::resource('/announcements', AdminAnnouncementController::class);

        // All bookings (ที่สร้างไว้)
        Route::get('/bookings',                    [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{id}/status',       [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');

        // Fields (สนามใหญ่)
        Route::resource('/fields', AdminFieldController::class);

        // Field Units (คอร์ต) — nested ใต้ field
        Route::prefix('/fields/{field}')->group(function () {
            Route::get   ('/units',              [FieldUnitController::class, 'index'])->name('fields.units.index');
            Route::get   ('/units/create',       [FieldUnitController::class, 'create'])->name('fields.units.create');
            Route::post  ('/units',              [FieldUnitController::class, 'store'])->name('fields.units.store');
            Route::get   ('/units/{unit}/edit',  [FieldUnitController::class, 'edit'])->name('fields.units.edit');
            Route::put   ('/units/{unit}',       [FieldUnitController::class, 'update'])->name('fields.units.update');
            Route::delete('/units/{unit}',       [FieldUnitController::class, 'destroy'])->name('fields.units.destroy');
        });
    });
});