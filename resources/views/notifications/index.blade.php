@extends('layouts.app')

@section('content')
    <div class="fms-card">
        <div class="fms-page-header">
            <h1 class="fms-page-title">Notifications</h1>
        </div>

        @if($notifications->count())
            <div class="fms-table-wrap">
                <table class="fms-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>When</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                            <tr>
                                <td>
                                    @if($notification->is_read)
                                        <span class="fms-badge" style="background-color: #e5e7eb; color: #374151;">Read</span>
                                    @else
                                        <span class="fms-badge">New</span>
                                    @endif
                                </td>
                                <td>{{ $notification->title }}</td>
                                <td>{{ Str::limit($notification->message, 80) }}</td>
                                <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    @if(!$notification->is_read)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="fms-link">Mark as read</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @else
            <p class="text-sm text-neutral-600">No notifications yet.</p>
        @endif
    </div>
@endsection