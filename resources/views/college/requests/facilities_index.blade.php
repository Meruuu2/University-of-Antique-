@extends('layouts.college')

@section('college-content')
<div class="fms-card">
    <div class="fms-page-header">
        <h1 class="fms-page-title">My Facilities Utilization Requests</h1>
        <a href="{{ route('college.requests.facilities.create') }}" class="fms-btn-primary">New Request</a>
    </div>

    @if ($submissions->count() > 0)
        <div class="fms-table-wrap">
            <table class="fms-table">
                <thead>
                    <tr>
                        <th>Date Requested</th>
                        <th>Activity Date</th>
                        <th>Time</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody> 
                    @foreach ($submissions as $submission)
                        <tr>
                            <td>{{ $submission->created_at->format('M d, Y') }}</td>
                            <td>{{ $submission->payload['date_activity'] ?? '-' }}</td>
                            <td>
                                {{ $submission->payload['time_range']['start'] ?? '-' }} -
                                {{ $submission->payload['time_range']['end'] ?? '-' }}
                            </td>
                            <td>{{ Str::limit($submission->payload['purpose'] ?? '-', 50) }}</td>
                            <td>
                                <span class="fms-badge">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('college.requests.facilities.show', $submission) }}" class="fms-link">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $submissions->links() }}
    @else
        <div class="py-12 text-center">
            <p class="mb-4 text-neutral-600">No facilities utilization requests found.</p>
            <a href="{{ route('college.requests.facilities.create') }}" class="fms-btn-primary">Submit Your First Request</a>
        </div>
    @endif
</div>
@endsection
