<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\SportsField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * ดึงเฉพาะ sports_field_id ที่ staff คนนี้รับผิดชอบ
     */
    private function getStaffFieldIds(): \Illuminate\Support\Collection
    {
        return SportsField::where('owner_id', Auth::id())
            ->pluck('id');
    }

    public function index(Request $request)
    {
        $fieldIds = $this->getStaffFieldIds();

        $query = Review::with(['user', 'sportsField'])
            ->whereIn('sports_field_id', $fieldIds); // 🔒 เฉพาะสนามของ staff

        // Filter sentiment
        if ($request->sentiment) {
            $query->where('sentiment', $request->sentiment);
        }

        $reviews = $query->latest()->paginate(10)->withQueryString();

        return view('staff.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $fieldIds = $this->getStaffFieldIds();

        if (!$fieldIds->contains($review->sports_field_id)) {
            abort(403, 'You do not have permission to view this review.');
        }

        return view('staff.reviews.show', compact('review'));
    }
}