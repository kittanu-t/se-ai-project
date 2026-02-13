@extends('layouts.app')
@section('title', 'Manage Units - '.$field->name)

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row">
        <div class="col-md-10">
            <h1 class="text-dark mb-4">Manage Units — {{ $field->name }}</h1>

            <div class="mb-3">
                <a href="{{ route('admin.fields.index') }}" 
                   class="btn fw-bold text-white" 
                   style="background-color: #6C757D; border: none; border-radius: 8px;">
                    ← Back to Fields
                </a>
                <a href="{{ route('admin.fields.units.create', $field) }}" 
                   class="btn fw-bold text-dark" 
                   style="background-color: #FFB900; border: none; border-radius: 8px;">
                    + Add Unit
                </a>
            </div>

            <div class="card p-4 shadow-sm rounded-4">
                @if(session('status'))
                    <div class="alert alert-info mb-4">{{ session('status') }}</div>
                @endif

                <!-- Units Table -->
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $u)
                            <tr>
                                <td>{{ $u->index }}</td>
                                <td>{{ $u->name }}</td>
                                <td>{{ ucfirst($u->status) }}</td>
                                <td>{{ $u->capacity }}</td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('admin.fields.units.edit', [$field, $u]) }}" 
                                       class="btn btn-sm fw-bold text-dark" 
                                       style="background-color: #FFB900; border: none; border-radius: 8px;">
                                        Edit
                                    </a>
                                    <form method="POST" 
                                          action="{{ route('admin.fields.units.destroy', [$field, $u]) }}" 
                                          onsubmit="return confirm('Delete unit?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm fw-bold text-white" 
                                                style="background-color: #E54D42; border: none; border-radius: 8px;">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-start">
                    {{ $units->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
