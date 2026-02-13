<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportsField;
use App\Models\FieldUnit;
use Illuminate\Http\Request;

class FieldUnitController extends Controller
{
    public function index(SportsField $field) // แสดงรายการ Unit ทั้งหมดของสนามที่เลือก
    {
        $units = $field->units()->orderBy('index')->paginate(50);
        return view('admin.units.index', compact('field','units'));
    }

    public function create(SportsField $field)
    {
        return view('admin.units.create', compact('field')); // แสดงฟอร์มสร้าง Unit ใหม่ภายใต้สนามที่เลือก
    }

    public function store(Request $request, SportsField $field) // บันทึก Unit ใหม่ลงฐานข้อมูลภายใต้สนามที่กำหนด
    {
        $data = $request->validate([
            'name'   => 'required|string|max:60',
            'index'  => 'required|integer|min:1',
            'status' => 'required|in:available,closed,maintenance',
            'capacity' => 'required|integer|min:0',
        ]);
        $data['sports_field_id'] = $field->id;

        FieldUnit::create($data);
        return redirect()->route('admin.fields.units.index', $field)->with('status','สร้าง Unit สำเร็จ');
    }

    public function edit(SportsField $field, FieldUnit $unit) // แสดงฟอร์มแก้ไข Unit โดยตรวจสอบว่าอยู่ในสนามที่ถูกต้อง
    {
        // ป้องกันแก้ unit ที่ไม่ได้อยู่ใต้ field นี้
        abort_if($unit->sports_field_id !== $field->id, 404);
        return view('admin.units.edit', compact('field','unit'));
    }

    public function update(Request $request, SportsField $field, FieldUnit $unit) // อัปเดตข้อมูล Unit ที่อยู่ในสนามนั้นหลังตรวจสอบข้อมูลแล้ว
    {
        abort_if($unit->sports_field_id !== $field->id, 404);

        $data = $request->validate([
            'name'   => 'required|string|max:60',
            'index'  => 'required|integer|min:1',
            'status' => 'required|in:available,closed,maintenance',
            'capacity' => 'required|integer|min:0',
        ]);

        $unit->update($data);
        return redirect()->route('admin.fields.units.index', $field)->with('status','อัปเดต Unit สำเร็จ');
    }

    public function destroy(SportsField $field, FieldUnit $unit) // ลบ Unit ที่อยู่ในสนามที่กำหนดออกจากฐานข้อมูล
    {
        abort_if($unit->sports_field_id !== $field->id, 404);
        $unit->delete();
        return redirect()->route('admin.fields.units.index', $field)->with('status','ลบ Unit สำเร็จ');
    }
}