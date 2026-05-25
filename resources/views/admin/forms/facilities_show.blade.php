@extends('layouts.admin')

@section('admin-content')
<div class="bg-white rounded shadow p-6">
    <h1 class="text-2xl font-semibold mb-4">Facilities Utilization Request #{{ $submission->id }}</h1>

    @if(session('status'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 border border-green-200 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 text-red-800 border border-red-200 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-6 text-sm">
        <div>
            <dt class="font-semibold text-gray-700">Requester</dt>
            <dd class="text-gray-800">
                {{ optional($submission->requester)->name ?? 'Unknown' }}
                <span class="text-xs text-gray-500">
                    ({{ $submission->requester_type }} – {{ $submission->requester_unit ?? 'N/A' }})
                </span>
            </dd>
        </div>
        <div>
            <dt class="font-semibold text-gray-700">Status</dt>
<dd class="text-gray-800">{{ $submission->status === 'booked' ? 'Booked' : ucfirst($submission->status) }}</dd>
        </div>
        <div>
            <dt class="font-semibold text-gray-700">Date Request (recorded)</dt>
            <dd class="text-gray-800">{{ $payload['date_request'] ?? '-' }}</dd>
        </div>
        <div>
            <dt class="font-semibold text-gray-700">Date of Activity</dt>
            <dd class="text-gray-800">{{ $payload['date_activity'] ?? '-' }}</dd>
        </div>
        <div>
            <dt class="font-semibold text-gray-700">Time</dt>
            <dd class="text-gray-800">
                @if(isset($payload['time_range']))
                    {{ $payload['time_range']['start'] ?? '' }} – {{ $payload['time_range']['end'] ?? '' }}
                @else
                    -
                @endif
            </dd>
        </div>
        <div>
            <dt class="font-semibold text-gray-700">Facility</dt>
            <dd class="text-gray-800">
                @if($facility)
                    {{ $facility->name }} ({{ $facility->location }})
                @else
                    Unknown (ID: {{ $payload['facility_id'] ?? 'N/A' }})
                @endif
            </dd>
        </div>
        <div class="md:col-span-2">
            <dt class="font-semibold text-gray-700">Purpose</dt>
            <dd class="text-gray-800">{{ $payload['purpose'] ?? '-' }}</dd>
        </div>

        <div>
            <dt class="font-semibold text-gray-700">Noted by (requester side)</dt>
            <dd class="text-gray-800">
                {{ $payload['noted_signatory_name'] ?? 'Not set' }}
                @if(!empty($payload['noted_datetime']))
                    <div class="text-xs text-gray-500">Date/time: {{ $payload['noted_datetime'] }}</div>
                @endif
            </dd>
        </div>
        <div>
            <dt class="font-semibold text-gray-700">Head of Office (GSU)</dt>
            <dd class="text-gray-800">
                {{ $payload['approved_head_name'] ?? 'Not set' }}
                @if(!empty($payload['approved_datetime']))
                    <div class="text-xs text-gray-500">Date/time: {{ $payload['approved_datetime'] }}</div>
                @endif
            </dd>
        </div>
    </dl>


    {{-- Equipment --}}
    @if(!empty($payload['equipment']))
        <h2 class="text-sm font-semibold text-gray-700 mb-2">Requested Equipment</h2>
        <table class="min-w-full divide-y divide-gray-200 mb-4 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($payload['equipment'] as $item => $qty)
                    @if($qty > 0)
                        <tr>
                            <td class="px-4 py-2 capitalize">{{ str_replace('_', ' ', $item) }}</td>
                            <td class="px-4 py-2 text-right">{{ $qty }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Actions --}}
    <div class="flex flex-wrap items-center gap-3 mt-4">
        @if($submission->status === 'pending')
            <form method="POST" action="{{ route('admin.forms.facilities.approve', $submission) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                    Approve
                </button>
            </form>
            <form method="POST" action="{{ route('admin.forms.facilities.disapprove', $submission) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                    Disapprove
                </button>
            </form>
        @endif

        @if(in_array($submission->status, ['approved','booked'], true))
            {{-- Generate PDF for the approved/booked request --}}
            <form method="GET" action="{{ route('admin.forms.facilities.pdf', $submission) }}">
                <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 text-sm">
                    Generate PDF
                </button>
            </form>
        @endif

        @if($submission->status === 'approved')
            {{-- Convert to booking after physical signing --}}
            <form method="POST" action="{{ route('admin.forms.facilities.set-booking', $submission) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    Set Booking
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
 