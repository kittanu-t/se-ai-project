@extends('layouts.app')

@section('title','Field Schedule')

@section('content')
<h1>Field Schedule</h1>

{{-- ตรวจสอบว่ามีสนามที่เจ้าหน้าที่ได้รับมอบหมายหรือไม่ --}}
@if($fields->isEmpty())
  <p>คุณยังไม่ได้รับมอบหมายสนาม</p>
@else
  <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <div>
      <label for="field-select">เลือกสนาม:</label>
      <select id="field-select">
        <option value="">-- เลือกสนาม --</option>
        {{-- ดึงข้อมูลสนามทั้งหมดจากตัวแปร $fields ที่ Controller ส่งมา --}}
        {{-- ข้อมูลมาจากฐานข้อมูลผ่าน Eloquent Model --}}
        @foreach($fields as $f)
          <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->sport_type }})</option>
        @endforeach
      </select>
    </div>
    <div>
      <label for="unit-select">เลือกคอร์ต:</label>
      <select id="unit-select" disabled>
        <option value="">-- เลือกคอร์ต --</option>
      </select>
    </div>
  </div>

  <div id="calendar" style="margin-top:16px;"></div>
@endif

<style>
  #calendar { max-width: 1100px; min-height: 600px; }
  .fc-booking-approved, .fc-booking-approved .fc-event-main { background:#4caf50!important; border-color:#4caf50!important; }
  .fc-booking-pending,  .fc-booking-pending  .fc-event-main { background:#ff9800!important; border-color:#ff9800!important; }
  .fc-closure { background: rgba(128,128,128,.35)!important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const { Calendar, dayGridPlugin, timeGridPlugin, interactionPlugin } = window.FC;

  const fieldSel = document.getElementById('field-select');
  const unitSel  = document.getElementById('unit-select');
  const calEl    = document.getElementById('calendar');

  // -------------------------------
  // ฟังก์ชันโหลด "คอร์ต" ของสนามที่เลือก
  // -------------------------------
  async function loadUnits(fieldId) {
    // เคลียร์ค่าเดิมของ select คอร์ต
    unitSel.innerHTML = '<option value="">-- เลือกคอร์ต --</option>';
    unitSel.disabled = true;
    if (!fieldId) return;

    // รับข้อมูลจาก API `/api/fields/{fieldId}/units`
    // -> Controller จะ query ข้อมูลจากฐานข้อมูลแล้วส่งกลับเป็น JSON
    const res = await fetch(`/api/fields/${fieldId}/units`, { credentials: 'same-origin' });
    const units = await res.json();

    // นำข้อมูลคอร์ตที่ได้มาเติมใน select
    units.forEach(u => {
      const opt = document.createElement('option');
      opt.value = u.id;
      opt.textContent = `${u.name} (${u.status})`;
      unitSel.appendChild(opt);
    });
    unitSel.disabled = false;
  }

  // -------------------------------
  // สร้างปฏิทินด้วย FullCalendar
  // -------------------------------
  const calendar = new Calendar(calEl, {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    height: 650,
    nowIndicator: true,
    allDaySlot: false,
    slotMinTime: '06:00:00',
    slotMaxTime: '23:00:00',
    headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },

    // -------------------------------
    // ดึงข้อมูลเหตุการณ์ (events) มาจาก API
    // -------------------------------
    events(fetchInfo, success, failure) {
      const fid = fieldSel.value;
      const uid = unitSel.value;

      // ถ้ายังไม่เลือกสนามหรือคอร์ต -> ไม่โหลดข้อมูล
      if (!fid || !uid) { success([]); return; }

      // รับข้อมูลจาก API `/api/fields/{fid}/units/{uid}/events`
      // ส่ง query parameters start และ end เพื่อบอกช่วงเวลาที่ต้องการโหลด
      // Controller จะไปดึงข้อมูลการจอง/ปิดสนาม จากฐานข้อมูล แล้วส่งกลับเป็น JSON
      const url = `/api/fields/${fid}/units/${uid}/events?start=${encodeURIComponent(fetchInfo.start.toISOString())}&end=${encodeURIComponent(fetchInfo.end.toISOString())}`;
      fetch(url, { credentials:'same-origin' })
        .then(r => { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
        .then(success)  // ส่งข้อมูล event ที่ได้ไปแสดงในปฏิทิน
        .catch(failure); // กรณีโหลดไม่สำเร็จ
    },

    // ตั้งค่าสีหรือคลาสของ event ตามประเภท (approved / pending / closure)
    eventClassNames: arg => arg.event.extendedProps.className || arg.event.classNames || [],
  });

  // แสดงปฏิทิน
  calendar.render();

  // -------------------------------
  // เมื่อเปลี่ยนสนาม -> โหลดคอร์ตใหม่ และรีเฟรชปฏิทิน
  // -------------------------------
  fieldSel?.addEventListener('change', async () => {
    await loadUnits(fieldSel.value);
    calendar.refetchEvents();
  });

  // -------------------------------
  // เมื่อเปลี่ยนคอร์ต -> โหลดเหตุการณ์ของคอร์ตนั้นใหม่
  // -------------------------------
  unitSel?.addEventListener('change', () => {
    calendar.refetchEvents();
  });
});
</script>
@endsection
