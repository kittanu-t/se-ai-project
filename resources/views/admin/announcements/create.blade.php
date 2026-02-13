@extends('layouts.app')
@section('title', 'Create Announcement')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center text-dark mb-4">Create Announcement</h1>

            <div class="card p-4 shadow-sm rounded-4">
                <form method="POST" action="{{ route('admin.announcements.store') }}" class="needs-validation">
                    @csrf

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold text-dark">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="form-control" id="title" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('title')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Audience -->
                    <div class="mb-3">
                        <label for="audience" class="form-label fw-bold text-dark">Audience</label>
                        <select name="audience" required class="form-select" id="audience" style="border: 1px solid #6C757D; border-radius: 8px;">
                            @foreach(['all', 'users', 'staff'] as $a)
                                <option value="{{ $a }}" @selected(old('audience') === $a)>{{ ucfirst($a) }}</option>
                            @endforeach
                        </select>
                        @error('audience')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Published At -->
                    <div class="mb-3">
                        <label for="published_at" class="form-label fw-bold text-dark">Published At (optional)</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="form-control" id="published_at" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('published_at')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="mb-3">
                        <label for="content" class="form-label fw-bold text-dark">Content</label>
                        <textarea name="content" rows="8" required class="form-control" id="content" style="border: 1px solid #6C757D; border-radius: 8px;">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-lg w-100 fw-bold text-dark" style="background-color: #FFB900; border: none; border-radius: 8px;">
                        Save
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
