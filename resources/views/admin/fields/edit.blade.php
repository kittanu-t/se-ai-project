@extends('layouts.app')
@section('title','Edit Field')
@section('content')
<div class="container-fluid py-4" style="background-color:#f8f9fa; min-height:100vh;">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <h1 class="text-center text-dark mb-4">Edit Field #{{ $field->id }}</h1>
      <div class="card p-4 shadow-sm rounded-4" style="background-color:#fff;">
        <form method="POST" action="{{ route('admin.fields.update',$field) }}">
          @csrf @method('PUT')

          <div class="mb-3">
            <label class="form-label fw-bold">Name</label>
            <input type="text" name="name" value="{{ old('name',$field->name) }}" required class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Sport Type</label>
            <input type="text" name="sport_type" value="{{ old('sport_type',$field->sport_type) }}" required class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Location</label>
            <input type="text" name="location" value="{{ old('location',$field->location) }}" required class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Capacity</label>
            <input type="number" min="0" name="capacity" value="{{ old('capacity',$field->capacity) }}" required class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select">
              @foreach(['available','closed','maintenance'] as $s)
                <option value="{{ $s }}" @selected(old('status',$field->status)===$s)>{{ $s }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Owner (staff)</label>
            <select name="owner_id" class="form-select">
              <option value="">-- none --</option>
              @foreach($staffs as $s)
                <option value="{{ $s->id }}" @selected(old('owner_id',$field->owner_id)==$s->id)>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Min Duration (min)</label>
            <input type="number" name="min_duration_minutes" min="15" value="{{ old('min_duration_minutes',$field->min_duration_minutes) }}" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Max Duration (min)</label>
            <input type="number" name="max_duration_minutes" min="15" value="{{ old('max_duration_minutes',$field->max_duration_minutes) }}" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Lead Time (hours)</label>
            <input type="number" name="lead_time_hours" min="0" value="{{ old('lead_time_hours',$field->lead_time_hours) }}" class="form-control">
          </div>

          <p class="mb-3">
            Units in this field: <strong>{{ $field->units_count }}</strong>
            <a href="{{ route('admin.fields.units.index',$field) }}" class="btn btn-sm fw-bold text-white" style="background-color:#4a5568; border-radius:6px; text-decoration:none;">Manage Units</a>
          </p>

          <button type="submit" class="btn fw-bold text-dark w-100" style="background-color:#facc15; border:none; border-radius:6px;">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
