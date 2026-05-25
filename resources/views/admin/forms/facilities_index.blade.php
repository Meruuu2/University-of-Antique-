@extends('layouts.admin')

@section('admin-content')
<div class="bg-white rounded shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">Facilities Utilization Requests</h1>
    </div>

    @if(session('status'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 border border-green-200 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if($submissions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($submissions as $submission)
                        <tr>
                            <td class="px-4 py-2">#{{ $submission->id }}</td>
                            <td class="px-4 py-2">
                                {{ optional($submission->requester)->name ?? 'Unknown' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $submission->requester_unit ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
@if($submission->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($submission->status === 'booked') bg-blue-100 text-blue-800
                                    @elseif($submission->status === 'approved') bg-green-100 text-green-800
                                    @elseif($submission->status === 'disapproved') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
{{ $submission->status === 'booked' ? 'Booked' : ucfirst($submission->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                {{ $submission->created_at?->format('Y-m-d H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.forms.facilities.show', $submission) }}" class="text-blue-600 hover:text-blue-900">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $submissions->links() }}
        </div>
    @else
        <p class="text-gray-600">No facilities utilization requests found.</p>
    @endif
</div>
@endsection
 