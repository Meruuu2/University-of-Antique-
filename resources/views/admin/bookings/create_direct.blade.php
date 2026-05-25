@extends('layouts.admin')
@section('title', 'Create Direct Booking')

@section('admin-content')
<div class="fms-card">
    <div class="fms-page-header border-0 pb-0 mb-4">
        <h1 class="fms-page-title">Create Direct Booking</h1>
        <a href="{{ route('admin.bookings.index') }}" class="fms-link">← Back to bookings</a>
    </div>

    <p class="mb-4 text-sm text-neutral-600">
        Use this only for urgent, high-priority bookings (emergency meetings, high-profile events).
        Conflicting bookings will be set to <strong>Pending</strong> and their requesters notified.
        Layout and fields mirror the college Facilities Utilization Request.
    </p>

    @if ($errors->any())
        <div class="fms-alert-error">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.bookings.store-direct') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Date & Time - same order/labels as college form --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="date_activity" class="fms-label">Date of Activity</label>
                <input
                    type="date"
                    name="date_activity"
                    id="date_activity"
                    class="fms-input"
                    required
                    value="{{ old('date_activity') }}"
                >
            </div>
            <div>
                <label for="start_time" class="fms-label">Start Time</label>
                <input
                    type="time"
                    name="start_time"
                    id="start_time"
                    class="fms-input"
                    required
                    value="{{ old('start_time') }}"
                >
            </div>
            <div>
                <label for="end_time" class="fms-label">End Time</label>
                <input
                    type="time"
                    name="end_time"
                    id="end_time"
                    class="fms-input"
                    required
                    value="{{ old('end_time') }}"
                >
            </div>
        </div>

        {{-- Venue (facilities) - mirror college "Venue" select, but allow multi-select for GSU --}}
        <div>
            <label for="facility_ids" class="fms-label">Venues to be utilized</label>
            <select
                name="facility_ids[]"
                id="facility_ids"
                class="fms-input"
                multiple
                required
            >
                @foreach($facilities as $facility)
                    <option
                        value="{{ $facility->id }}"
                        {{ in_array($facility->id, old('facility_ids', [])) ? 'selected' : '' }}
                    >
                        {{ $facility->name }} ({{ $facility->location }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-neutral-500">
                Hold Ctrl (Windows) / Cmd (Mac) to select multiple venues. This is the admin version of the college "Venue" field.
            </p>
        </div>

        {{-- Requester name (who this urgent booking is for) --}}
        <div>
            <label for="requester_name" class="fms-label">Requester Name / Unit</label>
            <input
                type="text"
                name="requester_name"
                id="requester_name"
                class="fms-input"
                required
                value="{{ old('requester_name') }}"
            >
            <p class="mt-1 text-xs text-neutral-500">
                Ex: "CCIS Dean", "CEA Program Head", "GSU Office", or specific office/organization.
            </p>
        </div>

        {{-- Purpose - same label as college --}}
        <div>
            <label for="purpose" class="fms-label">Purpose</label>
            <textarea
                name="purpose"
                id="purpose"
                rows="3"
                class="fms-input"
                required
            >{{ old('purpose') }}</textarea>
        </div>

        {{-- Equipment - mirror college style/labels --}}
        <div>
            <h2 class="text-sm font-semibold text-black mb-2">Facilities / Equipment to be used</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $equipmentFields = [
                        'qty_monobloc' => 'Monobloc Chair',
                        'qty_table'    => 'Long Table',
                        'qty_fan'      => 'Electric Fan',
                        'qty_rostrum'  => 'Rostrum',
                        'qty_flag'     => 'Flag & School Color',
                        'qty_sound'    => 'Sound',
                        'qty_led'      => 'LED Wall',
                    ];
                @endphp

                @foreach($equipmentFields as $name => $label)
                    <div>
                        <label class="fms-label">{{ $label }}</label>
                        <div class="flex items-center gap-2">
                            <button type="button" class="fms-btn-secondary px-2 py-1 text-xs"
                                    onclick="adjustQty('{{ $name }}', -10)">-10</button>
                            <button type="button" class="fms-btn-secondary px-2 py-1 text-xs"
                                    onclick="adjustQty('{{ $name }}', -1)">-1</button>
                            <input
                                type="number"
                                name="{{ $name }}"
                                id="{{ $name }}"
                                min="0"
                                class="fms-input w-20 text-center"
                                value="{{ old($name, 0) }}"
                            >
                            <button type="button" class="fms-btn-secondary px-2 py-1 text-xs"
                                    onclick="adjustQty('{{ $name }}', 1)">+1</button>
                            <button type="button" class="fms-btn-secondary px-2 py-1 text-xs"
                                    onclick="adjustQty('{{ $name }}', 10)">+10</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="fms-form-actions">
            <a href="{{ route('admin.bookings.index') }}" class="fms-btn-secondary">
                Cancel
            </a>
            <button type="submit" class="fms-btn-primary">
                Create Priority Booking
            </button>
        </div>
    </form>
</div>

<script>
function adjustQty(fieldId, delta) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    let value = parseInt(input.value || '0', 10);
    value += delta;
    if (value < 0) value = 0;
    input.value = value;
}
</script>
@endsection