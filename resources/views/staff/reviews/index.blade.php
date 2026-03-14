@extends('layouts.app') 

@section('title','Reviews')

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
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background: #fff;
  }
  th, td {
    padding: 8px 10px;
    border-bottom: 1px solid #eee;
    text-align: left;
  }
  th {
    background: #f9d71c;
    color: #222;
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
  .btn-red { background: #e63946; color: #fff; }
  .btn-gray { background: #ddd; color: #333; }
</style>

<h1>Reviews</h1>

<div class="card">

<form method="GET" action="{{ route('staff.reviews.index') }}" style="display:flex;gap:10px;align-items:center;">

    <select name="sentiment" class="btn btn-gray">
        <option value="">All Sentiment</option>
        <option value="positive" {{ request('sentiment')=='positive'?'selected':'' }}>Positive</option>
        <option value="neutral" {{ request('sentiment')=='neutral'?'selected':'' }}>Neutral</option>
        <option value="negative" {{ request('sentiment')=='negative'?'selected':'' }}>Negative</option>
    </select>

    <button type="submit" class="btn btn-yellow">
        Search
    </button>

    <a href="{{ route('staff.reviews.index') }}" class="btn btn-gray">
        Reset
    </a>

</form>

</div>

<div class="card">

<table>
<tr>
    <th>ID</th>
    <th>Review</th>
    <th>Rating</th>
    <th>Sentiment</th>
    <th>Action</th>
</tr>

@forelse($reviews as $review)

<tr>
    <td>{{ $review->id }}</td>

    <td>{{ $review->comment }}</td>

    <td>
        @for ($i = 1; $i <= 5; $i++)
            @if ($i <= $review->rating)
                ⭐
            @else
                ☆
            @endif
        @endfor
    </td>

    <td>
        @if($review->sentiment == 'negative')
            <span style="color:#e63946;font-weight:600;">Negative</span>
        @elseif($review->sentiment == 'positive')
            <span style="color:#2a9d8f;font-weight:600;">Positive</span>
        @else
            <span style="color:#555;">Neutral</span>
        @endif
    </td>

    <td>
        <a class="btn btn-yellow" href="{{ route('staff.reviews.show',$review->id) }}">
            View
        </a>
    </td>
</tr>

@empty
<tr>
<td colspan="5">No reviews found</td>
</tr>
@endforelse

</table>

<br>

{{ $reviews->links() }}

</div>

@endsection