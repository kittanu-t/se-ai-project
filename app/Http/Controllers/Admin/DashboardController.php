<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\SportsField;
use App\Models\User;
use App\Models\Review;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()  // แสดงสรุปข้อมูลบนแดชบอร์ด เช่น จำนวนผู้ใช้ สนาม การจอง และสถิติการใช้งานล่าสุด
    {
        // จำนวนทั้งหมด
        $totalBookings = Booking::count();
        $totalUsers    = User::count();
        $totalFields   = SportsField::count();

        // นับตามสถานะ
        $statusCounts = Booking::select('status', DB::raw('count(*) as c'))
            ->groupBy('status')->pluck('c','status');

        // Top 5 สนามที่ถูกจองมากที่สุด
        $topFields = Booking::select('sports_field_id', DB::raw('count(*) as c'))
            ->with('sportsField')
            ->groupBy('sports_field_id')
            ->orderByDesc('c')
            ->take(5)
            ->get();

        // Booking 30 วันล่าสุด
        $recentBookings = Booking::where('date','>=',Carbon::now()->subDays(30))
            ->count();

        // Utilization (คิดหยาบ ๆ = จำนวนการจอง / สนามทั้งหมด / 30 วัน)
        $utilization = $totalFields > 0
            ? round($recentBookings / $totalFields, 2)
            : 0;

        // ==================================================
        // แจ๊คเพิ่ม AI REVIEW ANALYSIS SECTION
        // ==================================================

        // -------------------------------
        // Sentiment Statistics
        // -------------------------------
        $sentimentStats = Review::select(
                'sentiment',
                DB::raw('count(*) as total')
            )
            ->groupBy('sentiment')
            ->pluck('total','sentiment');

        // -------------------------------
        // Fields with most negative reviews
        // -------------------------------
        $negativeFields = Review::where('sentiment','negative')
            ->select(
                'sports_field_id',
                DB::raw('count(*) as total')
            )
            ->with('sportsField')
            ->groupBy('sports_field_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // -------------------------------
        // Chart Data: Reviews per month (last 6 months)
        // -------------------------------
        $reviewsPerMonth = Review::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // -------------------------------
        // Send Data to View
        // -------------------------------
        return view('admin.dashboard', compact(
            'totalBookings',
            'totalUsers',
            'totalFields',
            'statusCounts',
            'topFields',
            'recentBookings',
            'utilization',
            'sentimentStats',
            'negativeFields',
            'reviewsPerMonth',
        ));
    }
}