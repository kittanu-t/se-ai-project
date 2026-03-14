<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::query();

        // Filter sentiment
        if ($request->sentiment) {
            $query->where('sentiment', $request->sentiment);
        }

        $reviews = $query->latest()->paginate(10)->withQueryString();

        return view('staff.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        return view('staff.reviews.show', compact('review'));
    }
}