@extends('layouts.admin')

@section('admin-content')
<div class="fms-card">
    <div class="fms-page-header">
        <h1 class="fms-page-title">Bookings (GSU overview)</h1>
        <a href="{{ route('admin.bookings.create-direct') }}" class="fms-button-primary">
            <i class="fas fa-plus mr-2"></i> Create Direct Booking
        </a>
    </div>

    @if($bookings->count())
        <div class="fms-table-wrap">
            <table class="fms-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Requester</th>
                        <th>Facility</th>
                        <th>When</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        <tr>
                            <td>{{ $booking->booking_code }}</td>
                            <td>
                                {{ optional($booking->requester)->name ?? 'Unknown' }}<br>
                                <span class="text-xs text-neutral-500">
                                    {{ $booking->requester_type }} – {{ $booking->requester_unit ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($booking->facilities->isNotEmpty())
                                    {{ $booking->facilities->pluck('name')->join(', ') }}
                                @else
                                    Unknown
                                @endif
                            </td>
                            <td>
                                {{ $booking->start_time?->format('M d, Y H:i') }} –
                                {{ $booking->end_time?->format('H:i') }}
                            </td>
                            <td><span class="fms-badge">{{ ucfirst($booking->status) }}</span></td>
                            <td>
                                <a href="{{ route('admin.bookings.edit', $booking) }}" class="fms-link">
                                    View / Modify
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    @else
        <p class="text-sm text-neutral-600">No bookings yet.</p>
    @endif
</div>
@endsection