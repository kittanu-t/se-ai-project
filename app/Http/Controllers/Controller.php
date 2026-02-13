<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementPublicController extends Controller
{
    // รายการประกาศที่เผยแพร่แล้ว สำหรับผู้ใช้ทั่วไป (audience: all, users)
    public function index(Request $request)
    {
        $q = Announcement::query()
            ->whereNotNull('published_at')
            ->whereIn('audience', ['all','users'])
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('title','like',"%$search%")
                  ->orWhere('content','like',"%$search%");
            });
        }

        $announcements = $q->paginate(15)->withQueryString();
        return view('announcements.public.index', compact('announcements'));
    }

    // หน้ารายละเอียดประกาศ
    public function show(Announcement $announcement)
    {
        // ป้องกันกดเข้าตรง ๆ ที่ยังไม่ publish หรือ audience ไม่ใช่ของ user
        abort_unless(
            $announcement->published_at &&
            in_array($announcement->audience, ['all','users']),
            404
        );

        return view('announcements.public.show', compact('announcement'));
    }
}