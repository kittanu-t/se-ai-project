@extends('layouts.app')
@section('title','My Bookings')

@section('content')
<div class="container">
  <div class="container-fluid py-4" style="background-color:#f4f6f8; min-height:100vh;">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h1 class="h4 fw-semibold mb-0">My Bookings</h1>
      <a href="{{ route('bookings.create') }}" class="btn btn-primary px-3 py-2">
        + New Booking
      </a>
    </div>

    {{-- Booking List --}}
    @forelse($bookings as $b)
      <div class="card card-soft mb-3">
        <div class="card-body">

          {{-- Top Row --}}
          <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">

            {{-- Left --}}
            <div>
              <strong>#{{ $b->id }}</strong> —
              <span class="fw-medium">
                {{ $b->sportsField->name ?? '-' }}
              </span>

              @php
                $unitName = $b->unit->name ?? null;
              @endphp

              @if($unitName)
                <span class="badge rounded-pill text-bg-light ms-2">
                  คอร์ต: {{ $unitName }}
                </span>
              @endif
            </div>

            {{-- Status --}}
            <div>
              @php
                $statusClass = match($b->status) {
                  'pending'   => 'status status-pending',
                  'approved'  => 'status status-approved',
                  'cancelled' => 'status status-cancelled',
                  'completed' => 'status status-completed',
                  default     => 'status'
                };
              @endphp
              <span class="{{ $statusClass }}">
                {{ ucfirst($b->status) }}
              </span>
            </div>

          </div>

          {{-- Date & Time --}}
          <div class="text-secondary small mb-3">
            {{ $b->date }} | {{ $b->start_time }} - {{ $b->end_time }}
          </div>

          {{-- Cancel Button --}}
          @if(!in_array($b->status, ['approved','completed','cancelled']))
            <form method="POST" action="{{ route('bookings.destroy', $b->id) }}">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-cancel">
                Cancel
              </button>
            </form>
          @endif

          {{-- ============================= --}}
          {{-- REVIEW SECTION --}}
          {{-- ============================= --}}
          @if($b->status === 'completed')

            {{-- If NOT reviewed yet --}}
            @if(!$b->review)

              <a href="{{ route('bookings.review.create', $b->id) }}"
                class="btn btn-sm btn-outline-primary">
                Write Review
              </a>

              <div id="review-form-{{ $b->id }}"
                   class="card mt-2 p-3"
                   style="display:none; background:#fafafa;">

                <form method="POST"
                      action="{{ route('bookings.review.store', $b->id) }}">
                  @csrf

                  {{-- Rating --}}
                  <div class="mb-3">
                    <label class="form-label fw-medium">Rating</label>
                    <select name="rating" class="form-select" required>
                      <option value="">Select rating</option>
                      @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">
                          {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                        </option>
                      @endfor
                    </select>
                  </div>

                  {{-- Comment --}}
                  <div class="mb-3">
                    <label class="form-label fw-medium">Comment</label>
                    <textarea name="comment"
                              class="form-control"
                              rows="3"
                              required></textarea>
                  </div>

                  <button type="submit"
                          class="btn btn-success btn-sm">
                    Submit Review
                  </button>

                </form>
              </div>

            {{-- If already reviewed --}}
            @else

              <div class="mt-3 p-3 rounded"
                   style="background:#f1f3f5;">

                <div class="fw-medium mb-1">
                  ⭐ {{ $b->review->rating }} / 5
                </div>

                <div class="small mb-1">
                  {{ $b->review->comment }}
                </div>

                <div class="small text-muted">
                  Sentiment:
                  {{ ucfirst($b->review->sentiment) }}
                  ({{ number_format($b->review->confidence_score, 2) }})
                </div>

              </div>

            @endif
          @endif

        </div>
      </div>

    @empty
      <div class="alert alert-light border rounded-4 shadow-sm text-center text-secondary">
        ยังไม่มีการจอง
      </div>
    @endforelse

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $bookings->links() }}
    </div>

  </div>
</div>
@endsection


@section('scripts')
<script>
function toggleReview(id) {
    const el = document.getElementById('review-form-' + id);
    if (!el) return;
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection