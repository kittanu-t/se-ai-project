@extends('layouts.app')
@section('title','Fields')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row">
        <div class="col-md-11">
            <h1 class="text-dark mb-4">Fields</h1>

            <div class="card p-4 shadow-sm rounded-4">
                {{-- Filter --}}
                <form method="GET" class="mb-4 d-flex gap-2 align-items-center flex-wrap">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name/type/location"
                           class="form-control" style="border:1px solid #6C757D; border-radius:8px; min-width:200px;">
                    
                    <button type="submit" class="btn fw-bold" 
                            style="background-color:#FFB900; color:#212529; border:none; border-radius:8px;">Search</button>
                    
                    <a href="{{ route('admin.fields.create') }}" 
                       class="btn fw-bold text-dark" 
                       style="background-color:#FFB900; border:none; border-radius:8px;">Create Field</a>
                </form>

                {{-- Status Message --}}
                @if(session('status'))
                    <div class="alert alert-info mb-4">{{ session('status') }}</div>
                @endif

                {{-- Table --}}
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Owner</th>
                            <th>Units</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fields as $f)
                        <tr>
                            <td>{{ $f->id }}</td>
                            <td>{{ $f->name }}</td>
                            <td>{{ $f->sport_type }}</td>
                            <td>{{ $f->location }}</td>
                            <td>{{ $f->status }}</td>
                            <td>{{ $f->owner?->name ?? '-' }}</td>
                            <td>{{ $f->units_count }}</td>
                            <td class="d-flex flex-wrap gap-1">
                                <a href="{{ route('admin.fields.edit',$f) }}" 
                                   class="btn btn-sm fw-bold text-dark" 
                                   style="background-color:#FFB900; border:none; border-radius:8px;">Edit</a>
                                
                                <a href="{{ route('admin.fields.units.index',$f) }}" 
                                   class="btn btn-sm fw-bold text-dark" 
                                   style="background-color:#E0E0E0; border:none; border-radius:8px;">Manage Units</a>
                                
                                <form method="POST" action="{{ route('admin.fields.destroy',$f) }}" 
                                      style="display:inline" onsubmit="return confirm('Delete field?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm fw-bold text-white" 
                                            style="background-color:#E54D42; border:none; border-radius:8px;">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-start mt-3">
                    {{ $fields->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
