@extends('layouts.app')
@section('title','Announcement Detail')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center text-dark mb-4">{{ $announcement->title }}</h1>

            <div class="card p-4 shadow-sm rounded-4">
                <p><strong>Audience:</strong> {{ ucfirst($announcement->audience) }}</p>
                <p><strong>Published:</strong> {{ $announcement->published_at }}</p>
                <p><strong>By:</strong> {{ $announcement->creator?->name }}</p>
                <hr>
                <div>{!! nl2br(e($announcement->content)) !!}</div>

                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn fw-bold text-dark" style="background-color: #FFB900; border: none; border-radius: 8px;">Edit</a>
                    <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn fw-bold text-white" style="background-color: #E54D42; border: none; border-radius: 8px;">Delete</button>
                    </form>
                    <a href="{{ route('admin.announcements.index') }}" class="btn fw-bold text-dark" style="background-color: #6C757D; border: none; border-radius: 8px;">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



