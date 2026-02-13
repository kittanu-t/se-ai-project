@extends('layouts.app')
@section('title','Edit Unit - '.$field->name)

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center text-dark mb-4">Edit Unit — {{ $field->name }}</h1>

            <div class="card p-4 shadow-sm rounded-4">
                <form method="POST" action="{{ route('admin.fields.units.update', [$field,$unit]) }}" class="needs-validation">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-dark">Name</label>
                        <input type="text" name="name" value="{{ old('name',$unit->name) }}" required class="form-control" id="name" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Index -->
                    <div class="mb-3">
                        <label for="index" class="form-label fw-bold text-dark">Index</label>
                        <input type="number" name="index" value="{{ old('index',$unit->index) }}" min="1" required class="form-control" id="index" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('index')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Capacity -->
                    <div class="mb-3">
                        <label for="capacity" class="form-label fw-bold text-dark">Capacity</label>
                        <input type="number" name="capacity" value="{{ old('capacity',$unit->capacity) }}" min="0" required class="form-control" id="capacity" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('capacity')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold text-dark">Status</label>
                        <select name="status" class="form-select" id="status" style="border: 1px solid #6C757D; border-radius: 8px;">
                            @foreach(['available','closed','maintenance'] as $s)
                                <option value="{{ $s }}" @selected(old('status',$unit->status)===$s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-lg w-100 fw-bold text-dark" style="background-color: #FFB900; border: none; border-radius: 8px;">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
