@extends('layouts.app')
@section('title','Create Field')

@section('content')
<div class="container-fluid py-4" style="background-color:#f8f9fa;min-height:100vh;">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <h1 class="text-center text-dark mb-4">Create Field</h1>

      <div class="card p-4 shadow-sm rounded-4" style="background-color:#fff;border-radius:15px;">
        <form method="POST" action="{{ route('admin.fields.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-bold">Name</label>
            <input name="name" value="{{ old('name') }}" required class="form-control" style="border-radius:8px;">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Sport Type</label>
            <input name="sport_type" value="{{ old('sport_type') }}" required class="form-control" style="border-radius:8px;">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Location</label>
            <input name="location" value="{{ old('location') }}" required class="form-control" style="border-radius:8px;">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Capacity</label>
            <input name="capacity" type="number" min="0" value="{{ old('capacity',0) }}" required class="form-control" style="border-radius:8px;">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select" style="border-radius:8px;">
              @foreach(['available','closed','maintenance'] as $s)
                <option value="{{ $s }}" @selected(old('status')===$s)>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Owner (staff)</label>
            <select name="owner_id" class="form-select" style="border-radius:8px;">
              <option value="">-- none --</option>
              @foreach($staffs as $s)
                <option value="{{ $s->id }}" @selected(old('owner_id')==$s->id)>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Min Duration (min)</label>
            <input name="min_duration_minutes" type="number" min="15" value="{{ old('min_duration_minutes',60) }}" class="form-control" style="border-radius:8px;">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Max Duration (min)</label>
            <input name="max_duration_minutes" type="number" min="15" value="{{ old('max_duration_minutes',180) }}" class="form-control" style="border-radius:8px;">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Lead Time (hours)</label>
            <input name="lead_time_hours" type="number" min="0" value="{{ old('lead_time_hours',1) }}" class="form-control" style="border-radius:8px;">
          </div>

          <button type="submit" class="btn fw-bold w-100 text-dark" style="background-color:#FFB900;border:none;border-radius:8px;">
            Save
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
