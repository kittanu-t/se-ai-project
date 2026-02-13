@extends('layouts.app')
@section('title','Create Booking')

@section('styles')
@endsection

@section('content')
<div class="container">
  <div class="container-fluid py-4" style="background-color:#f4f6f8; min-height:100vh;">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h1 class="h4 fw-semibold section-title mb-0">Create Booking</h1>
    </div>

      {{-- Flash / Errors --}}
      @if(session('status'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
          <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif

    <div class="row g-4">
      {{-- ซ้าย: ฟอร์ม --}}
      <div class="col-12 col-lg-6">
        <div class="card card-soft">
          <div class="card-body p-4">
            @php
              $prefield = request('field_id') ?? $prefield ?? null;
              $prefield_name = request('field_name');
            @endphp

            <form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
              @csrf

              {{-- เลือกสนาม --}}
              <div class="mb-3">
                <label for="sports_field_id" class="form-label">Field</label>
                <select id="sports_field_id" name="sports_field_id" class="form-select" required>
                  <option value="">-- Select Field --</option>
                  @foreach($fields as $f)
                    <option value="{{ $f->id }}" 
                      @selected(old('sports_field_id', $prefield)==$f->id)>
                      {{ $f->name }} ({{ $f->sport_type }})
                    </option>
                  @endforeach
                </select>

                @error('sports_field_id')
                  <div class="small text-danger mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- เลือกคอร์ต --}}
              <div class="mb-3">
                <label for="field_unit_id" class="form-label">Court</label>
                <select id="field_unit_id" name="field_unit_id" class="form-select" required>
                  <option value="">-- Select Court --</option>
                  @if($prefield)
                    @foreach(($fields->firstWhere('id',$prefield)?->units ?? []) as $u)
                      <option value="{{ $u->id }}" @selected(old('field_unit_id',$preunit)==$u->id)>
                        {{ $u->name }} ({{ $u->status }})
                      </option>
                    @endforeach
                  @endif
                </select>
                @error('field_unit_id')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
              </div>

              {{-- วันที่/เวลา/ติดต่อ --}}
              <div class="row g-3">
                <div class="col-12 col-sm-6">
                  <label for="date" class="form-label">Date</label>
                  <input id="date" type="date" name="date" value="{{ old('date') }}" class="form-control" required>
                  @error('date')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-6 col-sm-3">
                  <label for="start_time" class="form-label">Start</label>
                  <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" class="form-control" required>
                  @error('start_time')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-6 col-sm-3">
                  <label for="end_time" class="form-label">End</label>
                  <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" class="form-control" required>
                  @error('end_time')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                  <label for="contact_phone" class="form-label">Contact Phone</label>
                  <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="form-control" placeholder="เช่น 08x-xxx-xxxx">
                  @error('contact_phone')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                </div>
              </div>

              {{-- วัตถุประสงค์ --}}
              <div class="mb-3 mt-3">
                <label for="purpose" class="form-label">Purpose</label>
                <textarea id="purpose" name="purpose" rows="3" class="form-control" placeholder="ระบุวัตถุประสงค์การใช้งาน (ถ้ามี)">{{ old('purpose') }}</textarea>
                @error('purpose')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
              </div>

              <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary px-4">Submit Booking</button>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
              </div>
            </form>

          </div>
        </div>
      </div>

      {{-- ขวา: ปฏิทิน & legend --}}
      <div class="col-12 col-lg-6">
        <div class="card card-soft mb-3">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h2 class="h6 fw-semibold section-title mb-0">Availability</h2>
              <div class="small text-secondary">คลิก/ลากช่วงเวลาว่างเพื่อกรอกลงแบบฟอร์ม</div>
            </div>

            <div id="mini-calendar"></div>

            <div class="mt-3 small text-secondary">
              <span class="legend-dot" style="background:#E9ECEF;"></span> ปิดสนาม/คอร์ต
              <span class="ms-3 legend-dot" style="background:#28a745;"></span> Approved
              <span class="ms-3 legend-dot" style="background:#fd7e14;"></span> Pending
            </div>
          </div>
        </div>

        {{-- Tips --}}
        <div class="card card-soft">
          <div class="card-body p-4">
            <div class="d-flex align-items-center mb-2">
              <div class="rounded-circle me-2" style="width:10px;height:10px;background:#FFB900;"></div>
              <span class="fw-semibold" style="color:var(--txt-main);">Quick Tips</span>
            </div>
            <ul class="mb-0 small text-secondary ps-3">
              <li>เลือก Field ก่อน เพื่อโหลด Court และตารางเวลา</li>
              <li>ลากช่วงเวลาที่ว่างบนปฏิทิน ระบบจะเติม Date/Start/End อัตโนมัติ</li>
              <li>ช่วงเวลาจองแนะนำ ≥ 1 ชั่วโมง เพื่อหลีกเลี่ยงการชนกับคิวถัดไป</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- FullCalendar --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const fieldSel = document.getElementById('sports_field_id');
  const unitSel  = document.getElementById('field_unit_id');
  const dateInp  = document.getElementById('date');
  const startInp = document.getElementById('start_time');
  const endInp   = document.getElementById('end_time');

  //  เคลียร์ class เดิม (ไม่มี CSS ชื่อเหล่านี้แล้ว แต่คง logic ไว้)
  function updateBorders() {
    document.querySelectorAll('input, select, textarea').forEach(el => {
      el.classList.remove('input-filled', 'input-empty');
    });
  }
  document.addEventListener('input', updateBorders);
  document.addEventListener('change', updateBorders);
  updateBorders();

  // โหลด units เมื่อเลือกสนาม
  fieldSel.addEventListener('change', () => {
    const fid = fieldSel.value;
    unitSel.innerHTML = '<option value="">-- Select Court --</option>';
    if (!fid) { 
      calendar.refetchEvents(); 
      updateBorders();
      return; 
    }
    fetch(`/api/fields/${fid}/units`)
      .then(r => r.json())
      .then(units => {
        units.forEach(u => {
          const opt = document.createElement('option');
          opt.value = u.id;
          opt.textContent = `${u.name} (${u.status})`;
          unitSel.appendChild(opt);
        });
        calendar.refetchEvents();
        updateBorders();
      });
  });

  // FullCalendar
  const calEl = document.getElementById('mini-calendar');
  const calendar = new FullCalendar.Calendar(calEl, {
    initialView: 'timeGridWeek',
    height: 420,
    nowIndicator: true,
    allDaySlot: false,
    slotMinTime: '06:00:00',
    slotMaxTime: '23:00:00',
    headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
    events: (fetchInfo, success, failure) => {
      const fid = fieldSel.value, uid = unitSel.value;
      if (!fid || !uid) { success([]); return; }
      const url = `/api/fields/${fid}/units/${uid}/events?start=${encodeURIComponent(fetchInfo.start.toISOString())}&end=${encodeURIComponent(fetchInfo.end.toISOString())}`;
      fetch(url, { credentials: 'same-origin' })
        .then(r => r.json()).then(success).catch(failure);
    },
    eventClassNames: arg => [],  // คงตามที่คุณลบ CSS ภายใน
    selectable: true,
    select: (info) => {
      const pad = n => String(n).padStart(2,'0');
      const s = info.start, e = info.end;
      dateInp.value   = `${s.getFullYear()}-${pad(s.getMonth()+1)}-${pad(s.getDate())}`;
      startInp.value  = `${pad(s.getHours())}:${pad(s.getMinutes())}`;
      endInp.value    = `${pad(e.getHours())}:${pad(e.getMinutes())}`;
      updateBorders();
    },
    selectOverlap: () => false
  });
  calendar.render();

  unitSel.addEventListener('change', () => {
    calendar.refetchEvents();
    updateBorders();
  });

  if (fieldSel.value && unitSel.value) {
    calendar.refetchEvents();
    updateBorders();
  }

  dateInp.addEventListener('change', () => {
    if (dateInp.value) calendar.gotoDate(dateInp.value);
  });
});
</script>
@endsection
