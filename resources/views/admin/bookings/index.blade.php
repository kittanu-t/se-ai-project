@extends('layouts.app')
@section('title','All Bookings')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row">
        <div class="col-md-11">
            <h1 class="text-dark mb-4">All Bookings</h1>

            <div class="card p-4 shadow-sm rounded-4">
                {{-- Status / Error --}}
                @if(session('status'))
                    <div class="alert alert-info mb-3">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
                @endif

                {{-- Filter --}}
                <form method="GET" class="mb-4 d-flex gap-2 align-items-center flex-wrap">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; min-width:150px;">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; min-width:150px;">

                    <select name="status" class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; min-width:120px;">
                        <option value="">-- status --</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>

                    <select name="field_id" class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; min-width:120px;">
                        <option value="">-- field --</option>
                        @foreach($fields as $f)
                            <option value="{{ $f->id }}" @selected((string)request('field_id')===(string)$f->id)>{{ $f->name }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="q" value="{{ request('q') }}" placeholder="user name/email" 
                           class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; min-width:180px;">

                    <button type="submit" class="btn fw-bold" 
                            style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px;">Filter</button>
                    <a href="{{ route('admin.bookings.index') }}" 
                       class="btn fw-bold text-dark" style="background-color: #E0E0E0; border: none; border-radius: 8px;">Reset</a>
                </form>

                {{-- Table --}}
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Field</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $b)
                            <tr>
                                <td>#{{ $b->id }}</td>
                                <td>{{ $b->sportsField->name ?? '-' }}</td>
                                <td>{{ $b->user->name ?? '-' }} ({{ $b->user->email ?? '-' }})</td>
                                <td>{{ $b->date }}</td>
                                <td>{{ $b->start_time }} - {{ $b->end_time }}</td>
                                <td><strong>{{ $b->status }}</strong></td>
                                <td class="d-flex flex-wrap gap-1">
                                    {{-- Quick status update forms --}}
                                    <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button @disabled($b->status==='approved') 
                                                class="btn btn-sm fw-bold text-dark" 
                                                style="background-color: #FFB900; border: none; border-radius: 8px;">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button @disabled($b->status==='rejected') 
                                                class="btn btn-sm fw-bold text-white" 
                                                style="background-color: #E54D42; border: none; border-radius: 8px;">Reject</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button @disabled($b->status==='cancelled') 
                                                class="btn btn-sm fw-bold text-white" 
                                                style="background-color: #6C757D; border: none; border-radius: 8px;">Cancel</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.bookings.updateStatus',$b->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button @disabled($b->status==='completed') 
                                                class="btn btn-sm fw-bold text-white" 
                                                style="background-color: #198754; border: none; border-radius: 8px;">Complete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">ไม่พบข้อมูล</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-start mt-3">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
