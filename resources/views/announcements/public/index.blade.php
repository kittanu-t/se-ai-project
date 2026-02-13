@extends('layouts.app')
@section('title','Announcements')

@section('styles')
@endsection

@section('content')
<div class="container">
  <div class="container-fluid py-4" style="background-color:#f4f6f8; min-height:100vh;">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h1 class="h4 fw-semibold page-title mb-0">Announcements</h1>
    </div>
      
    {{-- Search Form --}}
    <form method="GET" class="row g-2 align-items-center mb-4">
      <div class="col-sm-10 col-12">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
              placeholder="ค้นหาหัวข้อหรือเนื้อหาประกาศ...">
      </div>
      <div class="col-sm-2 col-12 d-grid">
        <button class="btn btn-primary">ค้นหา</button>
      </div>
    </form>

    {{-- Announcements List --}}
    @forelse($announcements as $a)
      <div class="card card-soft mb-3">
        <div class="card-body">
          <h5 class="fw-semibold mb-1" style="color:var(--txt-main);">{{ $a->title }}</h5>
          <div class="announcement-meta mb-2">
            Audience: 
            <span class="text-capitalize">{{ $a->audience }}</span> 
            <span class="mx-2">|</span>
            Published: <span>{{ $a->published_at }}</span>
          </div>
          <div>
            <a href="{{ route('user.announcements.show', $a) }}" 
              class="text-decoration-none fw-medium"
              style="color:var(--act-red);">
              อ่านต่อ →
            </a>
          </div>
        </div>
      </div>
    @empty
      <div class="alert alert-light text-center text-secondary border rounded-4 shadow-sm">
        ยังไม่มีประกาศในขณะนี้
      </div>
    @endforelse

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $announcements->links() }}
    </div>
  </div>
</div>
@endsection
