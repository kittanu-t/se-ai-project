<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\SportsField;
use App\Models\FieldUnit;
use App\Models\FieldClosure;
use App\Models\Announcement;

class FieldController extends Controller
{
    // ==== LIST ====
    public function myFields(Request $request) // แสดงสนามที่สตาฟเป็น owner พร้อม units (เรียง index) และรายการปิดให้บริการที่ยัง active
    {
        $staff = $request->user();

        // สนามที่ตัวเองเป็น owner พร้อม units
        $fields = SportsField::with(['units' => function($q){
                $q->orderBy('index');
            }])
            ->where('owner_id', $staff->id)
            ->orderBy('name')
            ->get();

        // closure ที่ยัง active (ทั้งสนาม และเฉพาะคอร์ต)
        $fieldIds = $fields->pluck('id');
        $activeClosures = FieldClosure::whereIn('sports_field_id', $fieldIds)
            ->where('end_datetime', '>', now())
            ->orderByDesc('start_datetime')
            ->get()
            ->groupBy(function($c){
                // key แยกเป็น "field:{id}" หรือ "unit:{id}"
                return $c->field_unit_id ? 'unit:'.$c->field_unit_id : 'field:'.$c->sports_field_id;
            });

        return view('staff.fields.index', compact('fields','activeClosures'));
    }

    // ==== FIELD CLOSE/OPEN ====
    public function closeField(Request $request, $fieldId) // ปิดทั้งสนาม: บันทึก FieldClosure, อัปเดตสถานะสนาม, และสร้างประกาศแจ้งผู้ใช้
    {
        $staff = $request->user();
        $data = $request->validate([
            'reason'       => ['required','string','max:255'],
            'end_datetime' => ['nullable','date'],
            'status'       => ['nullable','in:closed,maintenance'],
        ]);

        $field = SportsField::where('owner_id', $staff->id)->findOrFail($fieldId);

        $start = now();
        $end   = !empty($data['end_datetime'])
               ? Carbon::parse($data['end_datetime'])
               : Carbon::parse('2099-12-31 23:59:59');

        if ($start->gte($end)) {
            return back()->withErrors(['end_datetime' => 'วันที่สิ้นสุดต้องอยู่หลังเวลาปัจจุบัน'])->withInput();
        }

        DB::transaction(function () use ($field, $staff, $data, $start, $end) {
            // ปิดทั้งสนาม: field_unit_id = NULL
            FieldClosure::create([
                'sports_field_id' => $field->id,
                'field_unit_id'   => null,
                'start_datetime'  => $start,
                'end_datetime'    => $end,
                'reason'          => $data['reason'],
                'created_by'      => $staff->id,
            ]);

            $field->status = $data['status'] ?? 'closed';
            $field->save();

            Announcement::create([
                'title'        => "สนาม {$field->name} ปิดชั่วคราว",
                'content'      => $data['reason']."\nช่วง: ".$start." - ".($end->year>=2099?'จนกว่าจะเปิดอีกครั้ง':$end),
                'audience'     => 'users',
                'created_by'   => $staff->id,
                'published_at' => now(),
            ]);
        });

        return back()->with('status', 'ปิดสนามทั้งก้อนเรียบร้อย');
    }

    public function openField(Request $request, $fieldId) // เปิดทั้งสนาม: ปิด (สิ้นสุด) closures ที่ยัง active ของสนาม แล้วสร้างประกาศเปิดบริการ
    {
        $staff = $request->user();
        $field = SportsField::where('owner_id', $staff->id)->findOrFail($fieldId);

        DB::transaction(function () use ($field, $staff) {
            // ปิดทุก closure ของสนาม (เฉพาะที่ยัง active และเป็นปิดทั้งสนาม)
            FieldClosure::where('sports_field_id', $field->id)
                ->whereNull('field_unit_id')
                ->where('end_datetime', '>', now())
                ->update(['end_datetime' => now()]);

            $field->status = 'available';
            $field->save();

            Announcement::create([
                'title'        => "สนาม {$field->name} เปิดให้บริการแล้ว",
                'content'      => "เปิดให้บริการตามปกติ ตั้งแต่ ".now(),
                'audience'     => 'users',
                'created_by'   => $staff->id,
                'published_at' => now(),
            ]);
        });

        return back()->with('status', 'เปิดสนามทั้งก้อนเรียบร้อย');
    }

    public function closeUnit(Request $request, $fieldId, $unitId) // ปิดเฉพาะยูนิต: บันทึก FieldClosure ของยูนิต, อัปเดตสถานะยูนิต, และสร้างประกาศ
    {
        $staff = $request->user();
        $data = $request->validate([
            'reason'       => ['required','string','max:255'],
            'end_datetime' => ['nullable','date'],
            'status'       => ['nullable','in:closed,maintenance'],
        ]);

        $field = SportsField::where('owner_id', $staff->id)->findOrFail($fieldId);
        $unit  = FieldUnit::where('sports_field_id', $field->id)->findOrFail($unitId);

        $start = now();
        $end   = !empty($data['end_datetime'])
               ? Carbon::parse($data['end_datetime'])
               : Carbon::parse('2099-12-31 23:59:59');

        if ($start->gte($end)) {
            return back()->withErrors(['end_datetime' => 'วันที่สิ้นสุดต้องอยู่หลังเวลาปัจจุบัน'])->withInput();
        }

        DB::transaction(function () use ($field, $unit, $staff, $data, $start, $end) {
            FieldClosure::create([
                'sports_field_id' => $field->id,
                'field_unit_id'   => $unit->id, // ปิดเฉพาะคอร์ต
                'start_datetime'  => $start,
                'end_datetime'    => $end,
                'reason'          => $data['reason'],
                'created_by'      => $staff->id,
            ]);

            $unit->status = $data['status'] ?? 'closed';
            $unit->save();

            Announcement::create([
                'title'        => "สนาม {$field->name} - ปิด {$unit->name} ชั่วคราว",
                'content'      => $data['reason']."\nช่วง: ".$start." - ".($end->year>=2099?'จนกว่าจะเปิดอีกครั้ง':$end),
                'audience'     => 'users',
                'created_by'   => $staff->id,
                'published_at' => now(),
            ]);
        });

        return back()->with('status', "ปิด {$unit->name} เรียบร้อย");
    }

    public function openUnit(Request $request, $fieldId, $unitId) // เปิดเฉพาะยูนิต: สิ้นสุด closures ของยูนิต, เปลี่ยนสถานะเป็น available, และสร้างประกาศ
    {
        $staff = $request->user();
        $field = SportsField::where('owner_id', $staff->id)->findOrFail($fieldId);
        $unit  = FieldUnit::where('sports_field_id', $field->id)->findOrFail($unitId);

        DB::transaction(function () use ($field, $unit, $staff) {
            FieldClosure::where('sports_field_id', $field->id)
                ->where('field_unit_id', $unit->id)
                ->where('end_datetime', '>', now())
                ->update(['end_datetime' => now()]);

            $unit->status = 'available';
            $unit->save();

            Announcement::create([
                'title'        => "สนาม {$field->name} - เปิด {$unit->name} แล้ว",
                'content'      => "{$unit->name} เปิดให้บริการตามปกติ ตั้งแต่ ".now(),
                'audience'     => 'users',
                'created_by'   => $staff->id,
                'published_at' => now(),
            ]);
        });

        return back()->with('status', "เปิด {$unit->name} เรียบร้อย");
    }
}