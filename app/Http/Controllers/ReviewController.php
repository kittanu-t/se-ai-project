<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;

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

        // 3️⃣ เรียก Python AI
        $process = new Process([
            'python',
            base_path('python/sentiment_ai.py'),
            $validated['comment']
        ]);

        $process->run();

        $sentiment = 'neutral';
        $confidence = 0.0000;

        if ($process->isSuccessful()) {
            $output = json_decode($process->getOutput(), true);

            if (isset($output['status']) && $output['status'] === 'success') {
                $sentiment = $output['label'];        // positive / neutral / negative
                $confidence = $output['score'];       // 0.9234
            }
        }

        // 4️⃣ สร้าง Review
        Review::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'sports_field_id' => $booking->sports_field_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'sentiment' => $sentiment,
            'confidence_score' => $confidence,
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Review submitted successfully.');
    }
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
}
