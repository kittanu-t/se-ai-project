<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\SportsField;
use App\Models\FieldClosure;
use App\Models\BookingLog;
use App\Models\FieldUnit;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['sportsField', 'unit'])
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->paginate(15);

        return view('user.bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        // โหลดสนามพร้อม units (สำหรับ prefill ตอนเปิดหน้าพร้อม field/unit จาก query)
        $fields   = \App\Models\SportsField::with('units')->orderBy('name')->get();
        $prefield = $request->query('field_id');       // ?field_id=ID
        $preunit  = $request->query('field_unit_id');  // ?field_unit_id=ID

        return view('user.bookings.create', compact('fields','prefield','preunit'));
    }

    public function store(Request $request)
    {
        // 1) Validate
        $data = $request->validate([
            'sports_field_id' => ['required','integer','exists:sports_fields,id'],
            'field_unit_id'   => ['required','integer','exists:field_units,id'], // คอร์ต
            'date'            => ['required','date'],
            'start_time'      => ['required','date_format:H:i'],
            'end_time'        => ['required','date_format:H:i','after:start_time'],
            'purpose'         => ['nullable','string'],
            'contact_phone'   => ['nullable','string','max:30'],
        ]);

        // 2) โหลดสนาม & คอร์ต และตรวจความสัมพันธ์
        $field = SportsField::findOrFail($data['sports_field_id']);
        $unit  = FieldUnit::where('sports_field_id', $field->id)
                    ->findOrFail($data['field_unit_id']);

        if ($field->status !== 'available') {
            return back()->withErrors(['sports_field_id' => 'สนามนี้ไม่พร้อมให้จองในขณะนี้'])->withInput();
        }
        if ($unit->status !== 'available') {
            return back()->withErrors(['field_unit_id' => 'คอร์ตนี้ไม่พร้อมให้จองในขณะนี้'])->withInput();
        }

        // 3) เตรียมเวลา
        $date      = Carbon::parse($data['date'])->toDateString(); // Y-m-d
        $startDT   = Carbon::parse($date.' '.$data['start_time'].':00'); // Y-m-d H:i:s
        $endDT     = Carbon::parse($date.' '.$data['end_time'].':00');

        // 4) Business rules
        // 4.1 ต้องเป็นอนาคต + lead time
        if (now()->gte($startDT)) {
            return back()->withErrors(['start_time' => 'เวลาเริ่มต้องอยู่ในอนาคต'])->withInput();
        }
        if (now()->diffInMinutes($startDT, false) < ($field->lead_time_hours * 60)) {
            return back()->withErrors(['start_time' => "ต้องจองล่วงหน้าอย่างน้อย {$field->lead_time_hours} ชั่วโมง"])
                        ->withInput();
        }
        // 4.2 ต้องอยู่ในวันเดียวกัน
        if ($startDT->toDateString() !== $endDT->toDateString()) {
            return back()->withErrors(['end_time' => 'การจองต้องอยู่ภายในวันเดียวกัน'])->withInput();
        }
        // 4.3 ระยะเวลาอยู่ใน min/max ของสนามใหญ่ (หรือถ้าต้องการใช้ของคอร์ตก็เปลี่ยนมาอิง $unit)
        $durationMinutes = $startDT->diffInMinutes($endDT);
        if ($durationMinutes < $field->min_duration_minutes) {
            return back()->withErrors(['end_time' => "ต้องไม่น้อยกว่า {$field->min_duration_minutes} นาที"])->withInput();
        }
        if ($durationMinutes > $field->max_duration_minutes) {
            return back()->withErrors(['end_time' => "ต้องไม่เกิน {$field->max_duration_minutes} นาที"])->withInput();
        }

        // 5) กันช่วงปิด (ทั้งสนาม หรือเฉพาะคอร์ต)
        $closureBlocked = FieldClosure::where('sports_field_id', $field->id)
            ->where(function($q) use ($unit) {
                $q->whereNull('field_unit_id')      // ปิดทั้งสนาม
                ->orWhere('field_unit_id', $unit->id); // หรือปิดคอร์ตนี้
            })
            ->where('start_datetime', '<', $endDT)
            ->where('end_datetime',   '>', $startDT)
            ->exists();

        if ($closureBlocked) {
            return back()->withErrors(['date' => 'ช่วงเวลานี้สนาม/คอร์ตปิดให้บริการ'])->withInput();
        }

        // 6) กันซ้อนกับการจองอื่น (ดูเฉพาะคอร์ตเดียวกัน และไม่นับ rejected/cancelled)
        $overlap = Booking::where('field_unit_id', $unit->id)
            ->where('date', $date)
            ->whereNotIn('status', ['rejected','cancelled'])
            ->where(function ($q) use ($data) {
                $q->where('start_time', '<', $data['end_time'].':00')
                ->where('end_time',   '>', $data['start_time'].':00');
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['start_time' => 'ช่วงเวลานี้มีการจองคอร์ตรายนี้แล้ว'])->withInput();
        }

        // 7) บันทึก + Log (transaction)
        DB::transaction(function () use ($data, $date, $field, $unit) {
            $booking = Booking::create([
                'user_id'         => auth()->id(),
                'sports_field_id' => $field->id,
                'field_unit_id'   => $unit->id,         // บันทึกคอร์ต
                'date'            => $date,
                'start_time'      => $data['start_time'].':00',
                'end_time'        => $data['end_time'].':00',
                'status'          => 'pending',
                'purpose'         => $data['purpose'] ?? null,
                'contact_phone'   => $data['contact_phone'] ?? null,
            ]);

            BookingLog::create([
                'booking_id' => $booking->id,
                'action'     => 'created',
                'by_user_id' => auth()->id(),
                'note'       => null,
                'created_at' => now(),
            ]);
        });

        return redirect()->route('bookings.index')->with('status', 'ส่งคำขอจองเรียบร้อย (รออนุมัติ)');
    }

    public function destroy($id)
    {
        $booking = Booking::where('user_id', auth()->id())->findOrFail($id);

        if (in_array($booking->status, ['approved','completed'])) {
            return back()->withErrors(['booking' => 'ไม่สามารถยกเลิกหลังอนุมัติ/เสร็จสิ้นแล้ว']);
        }

        DB::transaction(function () use ($booking) {
            $booking->status = 'cancelled';
            $booking->save();
            $booking->delete();

            BookingLog::create([
                'booking_id' => $booking->id,
                'action'     => 'cancelled',
                'by_user_id' => auth()->id(),
                'note'       => 'cancel by user',
                'created_at' => now(),
            ]);
        });

        return back()->with('status', 'ยกเลิกการจองแล้ว');
    }
}