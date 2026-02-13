@extends('layouts.app') 

@section('title','My Fields') 

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
  .status-alert {
    color: #b45309;
    font-size: 13px;
    margin-top: 4px;
  }
</style>

<h1>My Fields</h1>

{{-- แสดงข้อความแจ้งเตือน (รับจาก Controller ผ่าน session) --}}
@if(session('status')) 
  <div class="p-2 bg-green-100">{{ session('status') }}</div> 
@endif

{{-- แสดง error message ถ้ามี (รับจาก Controller ผ่าน validation) --}}
@if($errors->any())   
  <div class="p-2 bg-red-100">{{ $errors->first() }}</div>   
@endif

{{-- วนลูปแสดงรายการสนามที่เจ้าหน้าที่รับผิดชอบ (รับข้อมูลจาก Controller ผ่าน $fields) --}}
@forelse($fields as $f)
  @php
    // ตรวจสอบว่ามีการปิดสนามนี้อยู่หรือไม่
    // รับข้อมูลจาก Controller ผ่านตัวแปร $activeClosures ซึ่งมาจากฐานข้อมูล closures table
    $fieldKey = 'field:'.$f->id;
    $fieldActive = ($activeClosures[$fieldKey] ?? collect())->first();
  @endphp

  <div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
      <div>
        <strong>{{ $f->name }}</strong> ({{ $f->sport_type }})
        <div>Location: {{ $f->location }}</div>
        <div>Status: <strong>{{ $f->status }}</strong></div>

        {{-- ถ้าสนามนี้มีการปิดใช้งาน (ข้อมูลมาจากฐานข้อมูล closures ผ่าน Controller) --}}
        {{-- แค่แสดงผล ไม่ได้ส่งข้อมูลไปไหน --}}
        @if($fieldActive)
          <div class="status-alert">
            ปิดทั้งสนาม: {{ $fieldActive->first()->reason ?? '-' }} <br>
            ช่วง: {{ $fieldActive->first()->start_datetime }} - {{ $fieldActive->first()->end_datetime }}
          </div>
        @endif

      {{-- ปุ่มดูปฏิทินสนาม (ส่ง request GET ไปยัง route fields.show) --}}
      {{-- Controller จะใช้ข้อมูล $f->id เพื่อแสดงปฏิทินการจองของสนามนั้น --}}
      </div>
      <div>
        <a href="{{ route('fields.show', $f->id) }}" class="btn btn-gray">ดูปฏิทินสนามนี้</a>
      </div>
    </div>

    {{-- ส่วนควบคุมสถานะสนาม (เปิด / ปิดทั้งสนาม) --}}
    <div style="margin-top:10px;">
      {{-- ถ้าสนามถูกปิด → แสดงปุ่มเปิดสนาม --}}
      {{-- เมื่อกดจะส่ง POST ไปที่ route('staff.fields.open') เพื่อให้ Controller อัปเดต status เป็น available --}}
      @if($f->status !== 'available')
        <form method="POST" action="{{ route('staff.fields.open', $f->id) }}" style="display:inline;">
          @csrf
          <button type="submit" class="btn btn-yellow">เปิดสนาม (ทั้งก้อน)</button>
        </form>

      {{-- ถ้าสนามเปิดอยู่ → แสดงฟอร์มปิดสนาม --}}
      {{-- เมื่อกด submit จะส่ง POST ไปที่ route('staff.fields.close') เพื่อบันทึกเหตุผลและเวลาปิดใน DB --}}
      @else
        <form method="POST" action="{{ route('staff.fields.close', $f->id) }}" style="margin-top:8px;">
          @csrf
          <div>
            <label>เหตุผลปิดทั้งสนาม</label>
            <input type="text" name="reason" required placeholder="เช่น Maintenance / ทำความสะอาด">
          </div>
          <div style="margin-top:6px;">
            <label>สถานะ:</label>
            <select name="status">
              <option value="closed">closed</option>
              <option value="maintenance">maintenance</option>
            </select>
          </div>
          <div style="margin-top:6px;">
            <label>สิ้นสุดการปิด (เว้นว่าง = ปิดจนกว่าจะเปิดเอง)</label>
            <input type="datetime-local" name="end_datetime">
          </div>
          <button type="submit" class="btn btn-red" style="margin-top:8px;">ปิดทั้งสนาม และ ประกาศ</button>
        </form>
      @endif
    </div>

    {{-- ตารางแสดงคอร์ตทั้งหมดในสนาม --}}
    <div style="margin-top:12px;">
      <h4>Units / Courts</h4>
      <table>
        <tr>
          <th>ชื่อคอร์ต</th>
          <th>สถานะ</th>
          <th>การทำงาน</th>
        </tr>

        {{-- วนลูปคอร์ตแต่ละคอร์ตของสนาม --}}
        @forelse($f->units as $u)
          @php
            // ตรวจสอบว่าคอร์ตนี้มีการปิดใช้งานอยู่ไหม
            // ข้อมูลดึงจาก $activeClosures ที่ Controller เตรียมไว้จากฐานข้อมูล
            $ukey = 'unit:'.$u->id;
            $uActive = ($activeClosures[$ukey] ?? collect())->first();
          @endphp

          <tr>
            <td>{{ $u->name }}</td>
            <td>
              <strong>{{ $u->status }}</strong>
              {{-- ถ้ามีการปิดคอร์ตชั่วคราว --}}
              @if($uActive)
                <div class="status-alert">
                  ปิดชั่วคราว: {{ $uActive->first()->reason ?? '-' }} <br>
                  ช่วง: {{ $uActive->first()->start_datetime }} - {{ $uActive->first()->end_datetime }}
                </div>
              @endif
            </td>

            <td>
              {{-- ถ้าคอร์ตถูกปิด → แสดงปุ่มเปิดคอร์ต (ส่ง POST ไปยัง route staff.units.open) --}}
              {{-- Controller จะเปลี่ยนสถานะคอร์ตเป็น available --}}
              @if($u->status !== 'available')
                <form method="POST" action="{{ route('staff.units.open', [$f->id, $u->id]) }}" style="display:inline;">
                  @csrf 
                  <button type="submit" class="btn btn-yellow">เปิดคอร์ต</button>
                </form>

              {{-- ถ้าคอร์ตเปิดอยู่ → แสดงฟอร์มปิดคอร์ต --}}
              {{-- ส่ง POST ไปที่ route staff.units.close เพื่อบันทึกข้อมูลลงฐานข้อมูล --}}
              @else
                <form method="POST" action="{{ route('staff.units.close', [$f->id, $u->id]) }}" style="display:inline;">
                  @csrf
                  <input type="text" name="reason" required placeholder="เหตุผล" style="width:160px;">
                  <select name="status">
                    <option value="closed">closed</option>
                    <option value="maintenance">maintenance</option>
                  </select>
                  <input type="datetime-local" name="end_datetime">
                  <button type="submit" class="btn btn-red">ปิดคอร์ต และ ประกาศ</button>
                </form>
              @endif

              {{-- ปุ่มดูปฏิทินคอร์ต (ส่ง GET ไปที่ fields.show เพื่อแสดงรายละเอียดการจอง) --}}
              <a href="{{ route('fields.show', $f->id) }}" class="btn btn-gray" style="margin-left:6px;">ดูปฏิทิน</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="3">ยังไม่มีคอร์ตในสนามนี้</td></tr>
        @endforelse
      </table>
    </div>
  </div>
@empty
  {{-- ถ้าไม่มีสนามที่เจ้าหน้าที่ดูแล --}}
  <p>คุณยังไม่ได้รับมอบหมายให้ดูแลสนามใด ๆ</p>
@endforelse
@endsection
