@extends('layouts.app')
@section('title','Users')
@section('content')
<div class="container-fluid py-4" style="background-color:#f8f9fa; min-height:100vh;">
    <div class="row">
        <div class="col-md-10">
            <h1 class="text-dark mb-4">Users</h1>

            <div class="card p-4 shadow-sm rounded-4">
                <!-- Search + Create -->
                <form method="GET" class="mb-4 d-flex gap-2 flex-wrap align-items-center">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name/email"
                           class="form-control" style="border:1px solid #6C757D; border-radius:8px; min-width:200px;">
                    <button type="submit" class="btn fw-bold"
                            style="background-color:#FFB900; color:#212529; border:none; border-radius:8px;">
                        Search
                    </button>
                    <a href="{{ route('admin.users.create') }}" 
                       class="btn fw-bold"
                       style="background-color:#FFB900; color:#212529; border:none; border-radius:8px;">
                         Create
                    </a>
                </form>

                @if(session('status'))
                    <div class="alert alert-info mb-3">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
                @endif

                <!-- Users Table -->
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Active</th>
                            <th>Actions</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td>{{ $u->id }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show',$u) }}" 
                                       class="text-decoration-none text-dark">
                                        {{ $u->name }}
                                    </a>
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->role }}</td>
                                <td>{{ $u->active ? 'Yes' : 'No' }}</td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('admin.users.edit',$u) }}"
                                       class="btn btn-sm fw-bold text-dark"
                                       style="background-color:#FFB900; border:none; border-radius:8px;">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy',$u) }}" 
                                          style="display:inline" onsubmit="return confirm('Delete?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm fw-bold text-white"
                                                style="background-color:#E54D42; border:none; border-radius:8px;">
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
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
