@extends('layouts.app')
@section('title', $announcement->title)

@section('styles')
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      {{-- การ์ดประกาศ --}}
      <div class="card card-soft p-4 mb-4 bg-white">
        <div class="mb-3">
          <span class="badge rounded-pill px-3 py-2" style="background:#FFB900;color:#212529;">
            Announcement
          </span>
        </div>

        <h1 class="h4 fw-semibold mb-2 page-title">{{ $announcement->title }}</h1>

        <div class="announcement-meta mb-3">
          Audience: 
          <span class="text-capitalize">{{ $announcement->audience }}</span>
          <span class="mx-2">|</span>
          Published: 
          <span>{{ $announcement->published_at }}</span>
        </div>

        <hr>

        <div class="mt-3" style="color:var(--txt-main); line-height:1.7;">
          {!! nl2br(e($announcement->content)) !!}
        </div>
      </div>

      {{-- ปุ่มกลับ --}}
      <div class="text-start">
        <a href="{{ route('user.announcements.index') }}" 
           class="btn btn-dark px-4">
          ← กลับรายการประกาศ
        </a>
      </div>

    </div>
  </div>
</div>
@endsection
