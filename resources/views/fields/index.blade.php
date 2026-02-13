@extends('layouts.app')
@section('title','Fields')

@section('styles')
@endsection

@section('content')
<div class="container">
  <div class="container-fluid py-4" style="background-color:#f4f6f8; min-height:100vh;">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h1 class="h4 fw-semibold page-title mb-0">Sports Fields</h1>
    </div>

    {{-- ฟอร์มค้นหา --}}
    <form method="GET" class="row g-2 align-items-center mb-4">
      <div class="col-md-5 col-12">
        <input type="text" name="q" value="{{ request('q') }}" 
              class="form-control" placeholder="ค้นหาชื่อสนามหรือที่ตั้ง...">
      </div>
      <div class="col-md-3 col-6">
        <select name="sport_type" class="form-select">
          <option value="">-- ชนิดกีฬา --</option>
          @foreach($types as $t)
            <option value="{{ $t }}" @selected(request('sport_type')===$t)>{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 col-6 d-flex align-items-center">
        <div class="form-check me-3">
          <input class="form-check-input" type="checkbox" name="only_available" value="1" id="chkAvail"
                @checked(request('only_available'))>
          <label class="form-check-label text-secondary small" for="chkAvail">
            เฉพาะที่เปิดใช้งาน
          </label>
        </div>
      </div>
      <div class="col-md-1 col-12 d-grid">
        <button type="submit" class="btn btn-primary">ค้นหา</button>
      </div>
    </form>

    {{-- ตารางสนาม --}}
    <div class="card card-soft">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr class="text-secondary small">
                <th>ชื่อสนาม</th>
                <th>กีฬา</th>
                <th>ที่ตั้ง</th>
                <th>ความจุ</th>
                <th>สถานะ</th>
                <th class="text-center">การทำงาน</th>
              </tr>
            </thead>
            <tbody>
              @forelse($fields as $f)
                <tr>
                  <td class="fw-semibold">{{ $f->name }}</td>
                  <td>{{ $f->sport_type }}</td>
                  <td class="text-secondary small">{{ $f->location }}</td>
                  <td>{{ $f->capacity }}</td>
                  <td>
                    @switch(strtolower($f->status))
                      @case('available')
                        <span class="badge badge-available">Available</span>
                        @break
                      @case('unavailable')
                        <span class="badge badge-unavailable">Unavailable</span>
                        @break
                      @case('open')
                        <span class="badge bg-success">เปิด</span>
                        @break
                      @case('closed')
                        <span class="badge bg-secondary">ปิด</span>
                        @break
                      @default
                        <span class="badge bg-light text-dark">{{ ucfirst($f->status) }}</span>
                    @endswitch
                  </td>
                  <td class="text-center">
                    <a href="{{ route('fields.show', $f->id) }}" class="btn btn-outline-secondary btn-sm">
                      ดูปฏิทิน
                    </a>

                    @auth
                      @if(auth()->user()->role === 'user')
                        <a href="{{ route('bookings.create', ['field_id' => $f->id, 'field_name' => $f->name]) }}" 
                          class="btn btn-primary btn-sm ms-1">
                          จองสนามนี้
                        </a>
                      @endif
                    @endauth
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-secondary py-4">
                    ยังไม่มีข้อมูลสนาม
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $fields->links() }}
    </div>
  </div>
</div>
@endsection
