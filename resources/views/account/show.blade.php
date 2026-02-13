@extends('layouts.app')
@section('title','My Account')

@section('styles')
@endsection

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h4 fw-semibold section-title mb-0">My Account</h1>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
      <div class="alert alert-success border-0"> {{ session('success') }} </div>
    @endif

    {{-- Validation errors (แสดงแบบสวย) --}}
    @if ($errors->any())
      <div class="alert alert-danger border-0">
        <ul class="mb-0 ps-3">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="row g-4">
      {{-- Basic Info --}}
      <div class="col-12 col-lg-7">
        <div class="card card-soft">
          <div class="card-body p-4">
            <h3 class="h6 fw-semibold mb-3 section-title">Basic Info</h3>

            <form method="POST" action="{{ route('account.update') }}">
              @csrf @method('PUT')

              <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input id="name" type="text" name="name" class="form-control"
                      value="{{ old('name',$user->name) }}" />
              </div>

              <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" name="email" class="form-control"
                      value="{{ old('email',$user->email) }}" />
              </div>

              <div class="mb-3">
                <label class="form-label" for="phone">Phone</label>
                <input id="phone" type="text" name="phone" class="form-control"
                      value="{{ old('phone',$user->phone) }}" />
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Update Info</button>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- Change Password --}}
      <div class="col-12 col-lg-5">
        <div class="card card-soft">
          <div class="card-body p-4">
            <h3 class="h6 fw-semibold mb-3 section-title">Change Password</h3>

            <form method="POST" action="{{ route('account.password') }}">
              @csrf @method('PUT')

              <div class="mb-3">
                <label class="form-label" for="password">New Password</label>
                <input id="password" type="password" name="password" class="form-control" />
              </div>

              <div class="mb-3">
                <label class="form-label" for="password_confirmation">Confirm</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" />
              </div>

              <button type="submit" class="btn btn-primary w-100">Change Password</button>
            </form>

            <div class="small text-secondary mt-3">
              Tips: ใช้อักษรตัวพิมพ์ใหญ่/เล็ก ตัวเลข และอักขระพิเศษร่วมกันเพื่อความปลอดภัย
            </div>
          </div>
        </div>
      </div>
    </div>

@endsection
