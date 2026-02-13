@extends('layouts.app')

@section('title','Welcome')

@section('content')
<!-- HERO บนสุด -->
<div class="container-fluid py-4" style="background-color:#f4f6f8; min-height:100vh;">
  <div class="container">
    <div class="p-4 p-md-5 bg-white border rounded-4 shadow-sm">
      <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3">
        <div class="flex-grow-1">
          <h1 class="h3 h-lg-2 fw-semibold mb-2" style="color:#212529;">Sports Field Booking</h1>
          <p class="mb-0" style="color:#6C757D;">
            จองสนามกีฬาได้ง่าย รวดเร็ว และชัดเจน — อัปเดตประกาศล่าสุดอยู่ที่ด้านล่าง
          </p>
        </div>
      </div>
    </div>
  </div>
  
  <!-- ประกาศล่าสุด -->
  <section class="py-4">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h5 fw-semibold mb-0" style="color:#212529;">Latest Announcement</h2>
      </div>
      
      @if(!empty($announcements) && count($announcements))
      @php $a = $announcements->first(); @endphp
      <article class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
          <div class="d-flex align-items-start gap-3">
            <span class="badge rounded-pill" style="background:#FFB900;color:#212529;">
              [{{ strtoupper($a->audience) }}]
            </span>
            <div class="flex-grow-1">
              <h3 class="h6 fw-semibold mb-1" style="color:#212529;">{{ $a->title }}</h3>
              <div class="small" style="color:#6C757D;">{{ $a->published_at }}</div>
            </div>
          </div>
        </div>
      </article>
      @else
      <div class="alert alert-light border rounded-4" style="color:#6C757D;">ยังไม่มีประกาศ</div>
      @endif
    </div>
  </section>
  
  <!-- พื้นที่ต่อยอด -->
  <section class="pb-5">
    <div class="container">
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <div class="p-4 bg-white border rounded-4 shadow-sm h-100">
            <div class="d-flex align-items-center mb-2">
              <div class="rounded-circle me-2" style="width:10px;height:10px;background:#E54D42;"></div>
              <span class="fw-semibold" style="color:#212529;">Quick Tips</span>
            </div>
            <p class="mb-0" style="color:#6C757D;">ตรวจสอบประกาศก่อนจองสนาม เพื่อดูช่วงเวลาปิดปรับปรุงหรือโปรโมชั่นล่าสุด</p>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="p-4 bg-white border rounded-4 shadow-sm h-100">
            <div class="d-flex align-items-center mb-2">
              <div class="rounded-circle me-2" style="width:10px;height:10px;background:#FFB900;"></div>
              <span class="fw-semibold" style="color:#212529;">Friendly Reminder</span>
            </div>
            <p class="mb-0" style="color:#6C757D;">หากมีการเปลี่ยนแปลงสถานะการจอง คุณจะเห็นแจ้งเตือนที่มุมขวาบนของหน้าเว็บ</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
  @endsection
  