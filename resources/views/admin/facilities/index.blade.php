@extends('layouts.admin')

@section('admin-content')
<div class="fms-card">
    <div class="fms-page-header">
        <h1 class="fms-page-title">Facilities Management</h1>
        <a href="{{ route('admin.facilities.create') }}" class="fms-btn-primary">Add New Facility</a>
    </div>

    @if($facilities->count() > 0)
        <div class="fms-table-wrap">
            <table class="fms-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Owner</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead> 
                <tbody>
                    @foreach($facilities as $facility)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $facility->name }}</div>
                                <div class="text-xs text-neutral-600">{{ Str::limit($facility->description, 50) }}</div>
                            </td>
                            <td>{{ $facility->location }}</td>
                            <td>
                                @php
                                    $ownerLabel = match ($facility->owner_type) {
                                        'gsu'     => 'GSU',
                                        'college' => 'College',
                                        'org'     => 'Organization',
                                        default   => ucfirst($facility->owner_type ?? 'Unknown'),
                                    };
                                @endphp
                                <span class="fms-badge">{{ $ownerLabel }}</span>
                                @if($facility->owner_college)
                                    <span class="ml-1 text-xs text-neutral-500">({{ $facility->owner_college }})</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $status = $facility->availability_status ?? 'available';
                                @endphp
                                <span class="fms-badge">{{ ucfirst($status) }}</span>
                            </td>
                            <td class="space-x-3">
                                <a href="{{ route('admin.facilities.edit', $facility) }}" class="fms-link">Edit</a>
                                <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this facility?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="fms-link">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $facilities->links() }}</div>
    @else
        <div class="py-12 text-center">
            <p class="mb-4 text-neutral-600">No facilities found.</p>
            <a href="{{ route('admin.facilities.create') }}" class="fms-btn-primary">Add Your First Facility</a>
        </div>
    @endif
</div>
@endsection
