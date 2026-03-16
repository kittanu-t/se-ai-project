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
            {{-- AI Review Dashboard --}}
            {{-- ========================================= --}}
            <div class="card p-4 shadow-sm rounded-4 mb-4">
                <h3 class="mb-4">AI Review Dashboard</h3>

                <div class="row g-4">

                    {{-- Donut Chart: Sentiment --}}
                    <div class="col-md-5">
                        <h6 class="text-muted mb-3">Sentiment Breakdown</h6>
                        <canvas id="sentimentDonut" height="220"></canvas>
                    </div>

                    {{-- Bar Chart: Reviews per Month --}}
                    <div class="col-md-7">
                        <h6 class="text-muted mb-3">Reviews per Month (last 6 months)</h6>
                        <canvas id="reviewsBar" height="220"></canvas>
                    </div>

                </div>
            </div>
            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            // ── Donut: Sentiment ──────────────────────────────────
            new Chart(document.getElementById('sentimentDonut'), {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [
                        {{ $sentimentStats['positive'] ?? 0 }},
                        {{ $sentimentStats['neutral']  ?? 0 }},
                        {{ $sentimentStats['negative'] ?? 0 }},
                    ],
                    backgroundColor: ['#2a9d8f', '#adb5bd', '#e63946'],
                    borderWidth: 2,
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } },
                cutout: '65%',
            }
            });
            
            // ── Bar: Reviews per Month ────────────────────────────
            new Chart(document.getElementById('reviewsBar'), {
            type: 'bar',
            data: {
                labels: {!! $reviewsPerMonth->pluck('month')->toJson() !!},
                datasets: [{
                    label: 'Reviews',
                    data:  {!! $reviewsPerMonth->pluck('total')->toJson() !!},
                    backgroundColor: '#4361ee',
                    borderRadius: 6,
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                },
                plugins: { legend: { display: false } }
            }
            });
            </script>
            @endpush
        </div>
    </div>
</div>
@endsection
