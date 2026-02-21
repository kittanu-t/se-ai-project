@extends('layouts.app')
@section('title','รีวิวสนาม')

@section('content')
<div class="container py-4">
    <h4 class="mb-3">รีวิวสนาม: {{ $booking->sportsField->name }}</h4>

    <form method="POST" action="{{ route('bookings.review.store', $booking->id) }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">ให้คะแนน</label>
            <select name="rating" class="form-select" required>
                <option value="">เลือกคะแนน</option>
                @for($i=5;$i>=1;$i--)
                    <option value="{{ $i }}">{{ $i }} ดาว</option>
                @endfor
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">ความคิดเห็น</label>
            <textarea name="comment" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            ส่งรีวิว
        </button>
    </form>
</div>
@endsection