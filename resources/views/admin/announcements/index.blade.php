@extends('layouts.app')
@section('title','Announcements')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row">
        <div class="col-md-10">
            <h1 class="text-dark mb-4">Announcements</h1>

            <div class="card p-4 shadow-sm rounded-4">
                <!-- Filter Form + Create button -->
                <form method="GET" class="mb-4 d-flex gap-2 align-items-center flex-wrap">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title/content" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; min-width:150px;">
                    <select name="audience" class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; min-width:120px;">
                        <option value="">-- audience --</option>
                        @foreach(['all','users','staff'] as $a)
                            <option value="{{ $a }}" @selected(request('audience') === $a)>{{ ucfirst($a) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px;">Filter</button>
                    <a href="{{ route('admin.announcements.create') }}" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px;">Create</a>
                </form>

                @if(session('status'))
                    <div class="alert alert-info mb-4">{{ session('status') }}</div>
                @endif

                <!-- Announcements Table -->
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Audience</th>
                            <th>Published</th>
                            <th>By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($announcements as $a)
                            <tr>
                                <td>{{ $a->id }}</td>
                                <td><a href="{{ route('admin.announcements.show', $a) }}" class="text-decoration-none text-dark">{{ $a->title }}</a></td>
                                <td>{{ $a->audience }}</td>
                                <td>{{ $a->published_at }}</td>
                                <td>{{ $a->creator?->name }}</td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('admin.announcements.edit', $a) }}" class="btn btn-sm fw-bold text-dark" style="background-color: #FFB900; border: none; border-radius: 8px;">Edit</a>
                                    <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}" onsubmit="return confirm('Delete?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm fw-bold text-white" style="background-color: #E54D42; border: none; border-radius: 8px;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-start">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
