@extends('layouts.college')

@section('college-content')
<div class="bg-white rounded shadow p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Facilities Request #{{ $submission->id }}</h1>
        <a href="{{ route('college.requests.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm">
            Back to requests
        </a>
    </div>

    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
        <div>
            <dt class="font-semibold text-gray-700">Requested On</dt>
            <dd class="text-gray-800">{{ $submission->created_at?->format('Y-m-d H:i') ?? '-' }}</dd>
        </div>
        <div>
            <dt class="font-semibold text-gray-700">Status</dt>
            <dd class="text-gray-800">{{ ucfirst($submission->status) }}</dd>
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
        <div class="md:col-span-2">
            <dt class="font-semibold text-gray-700">Venues</dt>
            <dd class="text-gray-800">
                @if($facilities->isNotEmpty())
                    <ul class="list-disc list-inside">
                        @foreach($facilities as $facility)
                            <li>{{ $facility->name }} ({{ $facility->location }})</li>
                        @endforeach
                    </ul>
                @endif
                @if(!empty($payload['facility_name_custom']))
                    <p class="mt-2"><strong>Custom Venue:</strong> {{ $payload['facility_name_custom'] }}</p>
                @endif
                @if($facilities->isEmpty() && empty($payload['facility_name_custom']))
                    Unknown
                @endif
            </dd>
        </div>
        <div class="md:col-span-2">
            <dt class="font-semibold text-gray-700">Purpose</dt>
            <dd class="text-gray-800">{{ $payload['purpose'] ?? '-' }}</dd>
        </div>
    </dl>

    @if(!empty($payload['equipment']))
        <div class="mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Requested Equipment</h2>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
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
        </div>
    @endif
</div>
@endsection