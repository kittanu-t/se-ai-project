@extends('layouts.app')
@section('title','Edit User')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center text-dark mb-4">Edit User #{{ $user->id }}</h1>

            @if($errors->any())
                <div class="alert alert-danger rounded-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="card p-4 shadow-sm rounded-4">
                <form method="POST" action="{{ route('admin.users.update',$user) }}" class="needs-validation">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-dark">Name</label>
                        <input type="text" name="name" value="{{ old('name',$user->name) }}" required class="form-control" id="name" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold text-dark">Email</label>
                        <input type="email" name="email" value="{{ old('email',$user->email) }}" required class="form-control" id="email" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold text-dark">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone',$user->phone) }}" class="form-control" id="phone" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label fw-bold text-dark">Role</label>
                        <select name="role" required class="form-select" id="role" style="border: 1px solid #6C757D; border-radius: 8px;">
                            @foreach(['admin','staff','user'] as $r)
                                <option value="{{ $r }}" @selected(old('role',$user->role) === $r)>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Active -->
                    <div class="mb-3">
                        <label for="active" class="form-label fw-bold text-dark">Active</label>
                        <select name="active" class="form-select" id="active" style="border: 1px solid #6C757D; border-radius: 8px;">
                            <option value="1" @selected(old('active',$user->active)=='1')>Yes</option>
                            <option value="0" @selected(old('active',$user->active)=='0')>No</option>
                        </select>
                        @error('active')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold text-dark">Password <span class="text-muted">(leave blank to keep)</span></label>
                        <input type="password" name="password" class="form-control" id="password" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-bold text-dark">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" style="border: 1px solid #6C757D; border-radius: 8px;">
                        @error('password_confirmation')
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
