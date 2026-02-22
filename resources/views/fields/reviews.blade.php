@extends('layouts.app')

@section('content')

<div class="container">

<h2>{{ $field->name }} Reviews</h2>

{{-- ================= RATING SUMMARY ================= --}}
<div class="card p-3 mb-4">
    <h4>⭐ Average Rating</h4>

    <h2>{{ number_format($avgRating ?? 0,1) }}/5</h2>
    <p>{{ $totalReviews }} reviews</p>

    <hr>

    <h5>Customer Sentiment</h5>

    <p> Positive: {{ $sentimentSummary['positive'] ?? 0 }}</p>
    <p> Neutral: {{ $sentimentSummary['neutral'] ?? 0 }}</p>
    <p> Negative: {{ $sentimentSummary['negative'] ?? 0 }}</p>
</div>

{{-- ================= SORT ================= --}}
<form method="GET" class="mb-3">
    <select name="sort" onchange="this.form.submit()">
        <option value="">Sort by latest</option>
        <option value="rating"
            {{ request('sort')=='rating' ? 'selected' : '' }}>
            Sort by rating
        </option>
    </select>
</form>

{{-- ================= REVIEW LIST ================= --}}
@forelse($reviews as $review)

<div class="card mb-3 p-3">

    <strong>{{ $review->user->name }}</strong>

        {{-- ✅ วันที่รีวิว --}}
        <small class="text-muted">
            {{ optional($review->created_at)->format('d M Y ') }}
        </small>

    <div>⭐ {{ $review->rating }}/5</div>

    <p>{{ $review->comment }}</p>

    <small>
        @if($review->sentiment === 'positive')
            🟢 Positive
        @elseif($review->sentiment === 'negative')
            🔴 Negative
        @else
            🟡 Neutral
        @endif
    </small>

</div>

@empty
<p>No reviews yet.</p>
@endforelse

<div class="mt-3">
    {{ $reviews->withQueryString()->links() }}
</div>

</div>

@endsection