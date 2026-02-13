<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\BookingLog;
use App\Models\SportsField;
use App\Models\User;
use App\Models\UserNotification; 

class BookingController extends Controller
{
    public function index(Request $request) // แสดงรายการจองพร้อมตัวกรอง (สถานะ/สนาม/ช่วงวันที่/คำค้น) และส่งข้อมูลไปหน้า index
    {
        $q = Booking::with(['sportsField','user'])
            ->orderByDesc('date')->orderBy('start_time');

        // ฟิลเตอร์
        if ($s = $request->query('status')) {
            $q->where('status', $s);
        }
        if ($fid = $request->query('field_id')) {
            $q->where('sports_field_id', $fid);
        }
        if ($from = $request->query('date_from')) {
            $q->where('date', '>=', $from);
        }
        if ($to = $request->query('date_to')) {
            $q->where('date', '<=', $to);
        }
        if ($k = $request->query('q')) {
            $q->whereHas('user', function($w) use ($k) {
                $w->where('name','like',"%$k%")->orWhere('email','like',"%$k%");
            });
        }

        $bookings = $q->paginate(20)->withQueryString();

        // dropdown ช่วยกรอง
        $fields = SportsField::orderBy('name')->get(['id','name']);
        $statuses = ['pending','approved','rejected','cancelled','completed'];

        return view('admin.bookings.index', compact('bookings','fields','statuses'));
    }

    public function updateStatus(Request $request, $id) // เปลี่ยนสถานะการจองแบบทรานแซกชัน บันทึกล็อก และส่งโนติให้ผู้จอง
    {
        $data = $request->validate([
            'status' => ['required','in:pending,approved,rejected,cancelled,completed'],
            'note'   => ['nullable','string','max:255'],
        ]);

        DB::transaction(function () use ($id, $data, $request) {
            $b = Booking::lockForUpdate()->with('user','sportsField')->findOrFail($id);
            $old = $b->status;
            $new = $data['status'];

            // กติกาเปลี่ยนสถานะ (พื้นฐาน)
            if ($old === $new) return;

            if ($new === 'approved') {
                $b->approved_by = $request->user()->id;
                $b->approved_at = now();
            }
            if (in_array($new, ['rejected','cancelled']) && empty($b->approved_by)) {
                // อนุญาตให้ admin เป็นผู้ทำรายการ
                $b->approved_by = $request->user()->id;
                $b->approved_at = now();
            }

            $b->status = $new;
            $b->save();

            BookingLog::create([
                'booking_id' => $b->id,
                'action'     => $new, 
                'by_user_id' => $request->user()->id,
                'note'       => $data['note'] ?? null,
                'created_at' => now(),
            ]);

            // แจ้งเตือนผู้จอง
            UserNotification::create([
                'user_id' => $b->user_id,
                'type'    => 'booking.status.changed',
                'data'    => [
                    'booking_id' => $b->id,
                    'status'     => $new,
                    'message'    => "สถานะการจองเปลี่ยนเป็น {$new}",
                    'field'      => $b->sportsField?->name,
                    'date'       => $b->date,
                    'time'       => "{$b->start_time}-{$b->end_time}",
                ],
            ]);
        });

        return back()->with('status', 'อัปเดตสถานะเรียบร้อย');
    }
}