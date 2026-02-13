<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnouncementRequest;
use App\Http\Requests\Admin\UpdateAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request) // แสดงรายการประกาศทั้งหมด พร้อมค้นหาและกรองตาม audience
    {
        $q = Announcement::with('creator');

        if ($search = $request->query('q')) {
            $q->where(function($w) use ($search) {
                $w->where('title','like',"%$search%")
                  ->orWhere('content','like',"%$search%");
            });
        }
        if ($aud = $request->query('audience')) {
            $q->where('audience', $aud);
        }

        $announcements = $q->orderByDesc('published_at')
                           ->orderByDesc('created_at')
                           ->paginate(20)->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create() // แสดงฟอร์มสร้างประกาศใหม่
    {
        return view('admin.announcements.create');
    }

    public function store(StoreAnnouncementRequest $request) // บันทึกประกาศใหม่ลงฐานข้อมูลหลังตรวจสอบข้อมูลแล้ว
    {
        $data = $request->validated();

        Announcement::create([
            'title'       => $data['title'],
            'content'     => $data['content'],
            'audience'    => $data['audience'],
            'created_by'  => $request->user()->id,
            'published_at'=> $data['published_at'] ?? now(),
        ]);

        return redirect()->route('admin.announcements.index')->with('status','สร้างประกาศสำเร็จ');
    }

    public function show(Announcement $announcement) // แสดงรายละเอียดประกาศเฉพาะรายการ
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement) // แสดงฟอร์มแก้ไขประกาศที่เลือก
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement) // อัปเดตข้อมูลประกาศที่มีอยู่ในฐานข้อมูล
    {
        $data = $request->validated();

        $announcement->update([
            'title'       => $data['title'],
            'content'     => $data['content'],
            'audience'    => $data['audience'],
            'published_at'=> $data['published_at'],
        ]);

        return redirect()->route('admin.announcements.index')->with('status','อัปเดตประกาศสำเร็จ');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('status','ลบประกาศสำเร็จ'); // ลบประกาศออกจากฐานข้อมูล
    }
}