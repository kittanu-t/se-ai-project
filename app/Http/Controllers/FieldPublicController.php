<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SportsField;
use App\Models\Booking;
use App\Models\FieldClosure;
use Carbon\Carbon;
use App\Models\FieldUnit;

class FieldPublicController extends Controller
{
    public function index(Request $request)
    {
        $q = SportsField::query();

        // ค้นหาตามชื่อ/ที่ตั้ง
        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // กรองชนิดกีฬา
        if ($type = $request->query('sport_type')) {
            $q->where('sport_type', $type);
        }

        // เฉพาะสถานะ available
        if ($request->boolean('only_available')) {
            $q->where('status', 'available');
        }

        $fields = $q->orderBy('name')->paginate(12)->withQueryString();

        // รายการชนิดกีฬาไว้ทำ dropdown filter
        $types = SportsField::select('sport_type')->distinct()->orderBy('sport_type')->pluck('sport_type')->toArray();

        return view('fields.index', compact('fields', 'types'));
    }

    public function show($fieldId)
    {
        $field = SportsField::with('units')->findOrFail($fieldId);
        return view('fields.show', compact('field')); // แก้ path view ตามที่ใช้จริง
    }

    // JSON events สำหรับคอร์ต
    public function unitEvents(Request $request, $fieldId, $unitId)
    {
        $field = SportsField::findOrFail($fieldId);
        $unit  = FieldUnit::where('sports_field_id',$field->id)->findOrFail($unitId);

        $start = Carbon::parse($request->query('start'));
        $end   = Carbon::parse($request->query('end'));

        // Booking เฉพาะคอร์ตนี้
        $bookings = Booking::query()
            ->where('field_unit_id', $unit->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereNotIn('status', ['rejected','cancelled'])
            ->get();

        $bookingEvents = $bookings->map(function ($b) {
            $date = Carbon::parse($b->date)->toDateString();
            return [
                'id'        => 'booking-'.$b->id,
                'title'     => $b->status === 'pending' ? 'Pending booking' : 'Booked',
                'start'     => Carbon::parse("$date {$b->start_time}")->toIso8601String(),
                'end'       => Carbon::parse("$date {$b->end_time}")->toIso8601String(),
                'className' => [$b->status === 'approved' ? 'fc-booking-approved' : 'fc-booking-pending'],
            ];
        });

        // Closures: ทั้งที่ปิดทั้งสนาม หรือปิดเฉพาะคอร์ตนี้
        $closures = FieldClosure::query()
            ->where('sports_field_id', $field->id)
            ->where(function ($q) use ($unit) {
                $q->whereNull('field_unit_id')->orWhere('field_unit_id', $unit->id);
            })
            ->where(function ($q) use ($start, $end) {
                $q->where('start_datetime','<',$end)
                ->where('end_datetime','>',$start);
            })
            ->get();

        $closureEvents = $closures->map(function ($c) {
            return [
                'id'        => 'closure-'.$c->id,
                'title'     => $c->reason ? 'Closed: '.$c->reason : 'Closed',
                'start'     => Carbon::parse($c->start_datetime)->toIso8601String(),
                'end'       => Carbon::parse($c->end_datetime)->toIso8601String(),
                'display'   => 'background',
                'overlap'   => false,
                'className' => ['fc-closure'],
            ];
        });

        return response()->json($bookingEvents->merge($closureEvents)->values());
    }
}