{{-- resources/views/fields/show.blade.php --}}
@extends('layouts.app')
@section('title', $field->name)

@section('styles')
@endsection

@section('content')
<div class="container-fluid py-4" style="background-color:#f4f6f8; min-height:100vh;">
  {{-- Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
      <h1 class="h4 fw-semibold page-title mb-1">
        {{ $field->name }} <span class="text-secondary fw-normal">({{ $field->sport_type }})</span>
      </h1>
      <div class="small text-secondary">
        Location: <span>{{ $field->location }}</span>
        <span class="mx-2">|</span>
        Status:
        <span id="field-status" class="badge rounded-pill px-3 py-2">{{ $field->status }}</span>
      </div>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('fields.index') }}" class="btn btn-outline-secondary">← Back</a>
      @auth
        @if(auth()->user()->role === 'user')
          <a href="{{ route('bookings.create', ['field_id' => $field->id, 'field_name' => $field->name]) }}"
             class="btn btn-primary">
            จองสนามนี้
          </a>
        @endif
      @endauth
    </div>
  </div>

  {{-- Controls --}}
  <div class="card card-soft mb-3">
    <div class="card-body">
      <label for="unit-select" class="form-label mb-2">เลือกคอร์ท</label>
      <select id="unit-select" class="form-select w-auto">
        @foreach($field->units->sortBy('index') as $u)
          <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->status }})</option>
        @endforeach
      </select>
    </div>
  </div>

  {{-- Calendar --}}
  <div class="card card-soft">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="h6 fw-semibold mb-0">Calendar</h2>
        <div class="small text-secondary">
          <span class="legend-dot" style="background:#E9ECEF;"></span> ปิดสนาม/คอร์ท
          <span class="legend-dot ms-3" style="background:#28a745;"></span> Approved
          <span class="legend-dot ms-3" style="background:#fd7e14;"></span> Pending
        </div>
      </div>
      <div id="calendar"></div>
    </div>
  </div>
</div>

{{-- FullCalendar JS (ถ้ายังไม่ได้โหลดจากที่อื่น) --}}
<script>
  (function(){
    if (!window.FC){
      const s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js';
      document.head.appendChild(s);
    }
  })();
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // สีสถานะ Available / Unavailable (และ open/closed) — ทำฝั่งหน้าเพื่อไม่แตะ Blade logic
  (function(){
    const el = document.getElementById('field-status');
    if(!el) return;
    const t = (el.textContent || '').trim().toLowerCase();
    el.classList.remove('bg-success','bg-secondary','bg-danger','bg-light','text-dark');
    switch(t){
      case 'available':  el.classList.add('bg-success'); el.classList.remove('text-dark'); break; // เขียว
      case 'unavailable':el.classList.add('bg-danger');  break; // แดง (#E54D42 โทน Bootstrap danger)
      case 'open':       el.classList.add('bg-success'); break;
      case 'closed':     el.classList.add('bg-secondary'); break;
      default:           el.classList.add('bg-light','text-dark'); break;
    }
  })();

  // FullCalendar
  const waitForFC = () => {
    if (!window.FC){ return setTimeout(waitForFC, 50); }
    const { Calendar, dayGridPlugin, timeGridPlugin, interactionPlugin } = window.FC;

    const unitSelect = document.getElementById('unit-select');
    const calEl      = document.getElementById('calendar');
    const baseEvents = "{{ url('/api/fields/'.$field->id.'/units') }}";

    const cal = new Calendar(calEl, {
      plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
      initialView: 'timeGridWeek',
      height: 650,
      nowIndicator: true,
      allDaySlot: false,
      slotMinTime: '06:00:00',
      slotMaxTime: '23:00:00',
      headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
      events(fetchInfo, success, failure) {
        const unitId = unitSelect.value;
        if (!unitId) { success([]); return; }
        const url = `${baseEvents}/${unitId}/events?start=${encodeURIComponent(fetchInfo.start.toISOString())}&end=${encodeURIComponent(fetchInfo.end.toISOString())}`;
        fetch(url, { credentials:'same-origin' })
          .then(r => { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
          .then(success)
          .catch(failure);
      },
      eventClassNames: arg => arg.event.extendedProps.className || arg.event.classNames || [],
      eventDidMount(info){ if (info.event.title) info.el.title = info.event.title; },
    });

    cal.render();
    unitSelect.addEventListener('change', () => cal.refetchEvents());
    if (unitSelect.value) cal.refetchEvents();
  };
  waitForFC();
});
</script>
@endsection
