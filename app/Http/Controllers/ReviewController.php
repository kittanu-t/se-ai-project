<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Models\SportsField;
use Illuminate\Support\Facades\Http;

class ReviewController extends Controller
{
    public function store(Request $request, $bookingId)
    {
        $user = Auth::user();

        // 1️⃣ Validate
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:5'],
        ]);

        // 2️⃣ หา booking
        $booking = Booking::with('review')->findOrFail($bookingId);

        // 🔒 Security checks
        if ($booking->user_id !== $user->id) {
            abort(403);
        }

        if ($booking->status !== 'completed') {
            return back()->withErrors('You can only review completed bookings.');
        }

        if ($booking->review) {
            return back()->withErrors('You already reviewed this booking.');
        }

        // =====================================================
        // 3️⃣ สร้าง Review ก่อนเลย (ไม่รอ AI)
        // sentiment เริ่มต้นเป็น 'pending' ก่อน
        // =====================================================

        $review = Review::create([
            'booking_id'       => $booking->id,
            'user_id'          => $user->id,
            'sports_field_id'  => $booking->sports_field_id,
            'rating'           => $validated['rating'],
            'comment'          => $validated['comment'],
            'sentiment'        => 'pending',
            'confidence_score' => 0.0000,
        ]);

        // =====================================================
        // 4️⃣ เรียก Sentiment AI หลัง response ส่งกลับ user แล้ว
        // user ไม่ต้องรอ — sentiment จะอัพเดตใน background
        // =====================================================

        dispatch(function () use ($review) {
            try {
                $flaskUrl = env('FLASK_API_URL', 'http://127.0.0.1:8001');

                $response = Http::timeout(120)
                    ->post($flaskUrl . '/analyze', [
                        'text' => $review->comment,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['status']) && $data['status'] === 'success') {
                        $review->update([
                            'sentiment'        => $data['label'],
                            'confidence_score' => $data['score'],
                        ]);
                    }
                }

            } catch (\Exception $e) {
                // ถ้า AI ล่ม → sentiment ยังคงเป็น 'pending'
                logger()->error('Sentiment API error: ' . $e->getMessage());
            }
        })->afterResponse();

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Review submitted successfully.');
    }

    // =====================================================
    // Create Review Page
    // =====================================================
    public function create($bookingId)
    {
        $booking = Booking::with('review')->findOrFail($bookingId);

        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'completed') {
            return redirect()->route('bookings.index')
                ->withErrors('You can only review completed bookings.');
        }

        if ($booking->review) {
            return redirect()->route('bookings.index')
                ->withErrors('You already reviewed this booking.');
        }

        return view('user.reviews.create', compact('booking'));
    }

    // =====================================================
    // Field Reviews Page
    // =====================================================
    public function fieldReviews($fieldId, Request $request)
    {
        $field = SportsField::findOrFail($fieldId);

        $query = Review::with('user')
            ->where('sports_field_id', $fieldId);

        if ($request->sort === 'rating') {
            $query->orderByDesc('rating');
        } else {
            $query->latest();
        }

        $reviews = $query->paginate(10);

        $avgRating = Review::where('sports_field_id', $fieldId)->avg('rating');

        $totalReviews = Review::where('sports_field_id', $fieldId)->count();

        $sentimentSummary = Review::selectRaw("
                sentiment,
                COUNT(*) as total
            ")
            ->where('sports_field_id', $fieldId)
            ->groupBy('sentiment')
            ->pluck('total', 'sentiment');

        return view('fields.reviews', compact(
            'field',
            'reviews',
            'avgRating',
            'totalReviews',
            'sentimentSummary'
        ));
    }
}