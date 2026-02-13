@extends('layouts.app')
@section('title', 'User Detail')
@section('content')
<h1 style="margin-bottom: 15px;">User #{{ $user->id }}</h1>

<div class="card p-3 shadow-sm rounded-4" style="border: 1px solid #6C757D;">
    <p style="margin-bottom: 10px;">Name: {{ $user->name }}</p>
    <p style="margin-bottom: 10px;">Email: {{ $user->email }}</p>
    <p style="margin-bottom: 10px;">Role: {{ $user->role }}</p>
    <p>Active: {{ $user->active ? 'Yes' : 'No' }}</p>
</div>

<p style="margin-top: 15px;">
    <a href="{{ route('admin.users.index') }}" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 5px 10px; text-decoration: none;">← Back to Users</a>
</p>
@endsection