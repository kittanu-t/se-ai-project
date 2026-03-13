@extends('layouts.app') 

@section('title','Review Detail')

@section('content')

<style>
  .card {
    background: #fff;
    padding: 16px;
    margin-bottom: 18px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }
  h1 {
    font-weight: bold;
    margin-bottom: 18px;
  }
  .btn {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
  }
  .btn-yellow { background: #f9d71c; color: #000; }
</style>

<h1>Review Detail</h1>

<div class="card">

<p>
<strong>Review:</strong><br>
{{ $review->comment }}
</p>

<br>

<p>
<strong>Rating:</strong><br>

@for ($i = 1; $i <= 5; $i++)
    @if ($i <= $review->rating)
        ⭐
    @else
        ☆
    @endif
@endfor

</p>

<br>

<p>
<strong>Sentiment:</strong>

@if($review->sentiment == 'negative')
    <span style="color:#e63946;font-weight:600;">Negative</span>
@elseif($review->sentiment == 'positive')
    <span style="color:#2a9d8f;font-weight:600;">Positive</span>
@else
    <span style="color:#555;">Neutral</span>
@endif

</p>

<br>

<a href="{{ route('staff.reviews.index') }}" class="btn btn-yellow">
← Back
</a>

</div>

@endsection