@extends('layouts.app')
@section('title','Admin Dashboard')

@section('content')
<div class="container-fluid py-4 rounded-3" style="background-color:#f8f9fa; min-height:100vh;">
    <div class="row">
        <div class="col-md-10">
            <h1 class="text-dark mb-4">Admin Dashboard</h1>

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-2">
                    <div class="card p-3 shadow-sm rounded-4">
                        <strong>Total Bookings:</strong> {{ $totalBookings }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="card p-3 shadow-sm rounded-4">
                        <strong>Total Users:</strong> {{ $totalUsers }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="card p-3 shadow-sm rounded-4">
                        <strong>Total Fields:</strong> {{ $totalFields }}
                    </div>
                </div>
            </div>

            {{-- Bookings by Status --}}
            <div class="card p-4 shadow-sm rounded-4 mb-4">
                <h3 class="mb-3">Bookings by Status</h3>
                <ul class="list-group">
                    @foreach($statusCounts as $status => $c)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ ucfirst($status) }}
                            <span class="badge bg-primary rounded-pill">{{ $c }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Top 5 Fields --}}
            <div class="card p-4 shadow-sm rounded-4 mb-4">
                <h3 class="mb-3">Top 5 Fields by Booking Count</h3>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Bookings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topFields as $row)
                            <tr>
                                <td>{{ $row->sportsField->name ?? '-' }}</td>
                                <td>{{ $row->c }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Utilization --}}
            <div class="card p-4 shadow-sm rounded-4 mb-4">
                <h3 class="mb-3">Utilization (last 30 days)</h3>
                <p>{{ $utilization }} bookings/field (avg over 30 days)</p>
            </div>

            {{-- แจ๊คเพิ่ม========================================= --}}
            {{-- AI Sentiment Statistics --}}
            {{-- ========================================= --}}
            <div class="card p-4 shadow-sm rounded-4 mb-4">

                <h3 class="mb-3">AI Review Sentiment Statistics</h3>

                <ul class="list-group">

                    <li class="list-group-item d-flex justify-content-between">
                        Positive
                        <span class="badge bg-success">
                            {{ $sentimentStats['positive'] ?? 0 }}
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        Neutral
                        <span class="badge bg-secondary">
                            {{ $sentimentStats['neutral'] ?? 0 }}
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        Negative
                        <span class="badge bg-danger">
                            {{ $sentimentStats['negative'] ?? 0 }}
                        </span>
                    </li>

                </ul>

            </div>

            {{-- ========================================= --}}
            {{-- Most Negative Fields --}}
            {{-- ========================================= --}}
            <div class="card p-4 shadow-sm rounded-4 mb-4">

                <h3 class="mb-3">Fields with Most Negative Reviews</h3>

                <table class="table table-striped">

                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Negative Reviews</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($negativeFields as $field)

                        <tr>
                            <td>{{ $field->sportsField->name ?? '-' }}</td>
                            <td>{{ $field->total }}</td>
                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            {{-- ========================================= --}}
            {{-- AI Summary Report --}}
            {{-- ========================================= --}}
            <div class="card p-4 shadow-sm rounded-4 mb-4">

                <h3 class="mb-3">AI Review Summary</h3>

                <p>
                    {{ $summary }}
                </p>

            </div>

        </div>
    </div>

</div>
@endsection
