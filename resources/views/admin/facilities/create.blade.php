@extends('layouts.admin')

@section('admin-content')
<div class="fms-card">
    <div class="fms-page-header">
        <h1 class="fms-page-title">Add New Facility</h1>
        <a href="{{ route('admin.facilities.index') }}" class="fms-link">← Back to Facilities</a>
    </div>

    <form method="POST" action="{{ route('admin.facilities.store') }}" class="space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Facility Name</label>
            <input type="text" name="name" id="name" required
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                   value="{{ old('name') }}">
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
            <input type="text" name="location" id="location" required
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                   value="{{ old('location') }}">
            @error('location')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="owner_type" class="block text-sm font-medium text-gray-700">Owner Type</label>
            <select name="owner_type" id="owner_type" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Owner Type</option>
                <option value="gsu" {{ old('owner_type') == 'gsu' ? 'selected' : '' }}>GSU (General Services Unit)</option>
                <option value="college" {{ old('owner_type') == 'college' ? 'selected' : '' }}>College</option>
                <option value="org" {{ old('owner_type') == 'org' ? 'selected' : '' }}>Organization</option>
            </select>
            @error('owner_type')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div id="college-field" class="hidden">
            <label for="owner_college" class="block text-sm font-medium text-gray-700">College Name</label>
            <input type="text" name="owner_college" id="owner_college"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                   value="{{ old('owner_college') }}"
                   placeholder="e.g., College of Engineering">
            @error('owner_college')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center mb-4">
            <input type="checkbox" name="is_active" id="is_active" value="1" checked
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                Active (facility enabled in the system)
            </label>
        </div>

        <div>
            <label for="availability_status" class="block text-sm font-medium text-gray-700">Availability Status</label>
            <select name="availability_status" id="availability_status"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="available" {{ old('availability_status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="unavailable" {{ old('availability_status', 'available') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                <option value="maintenance" {{ old('availability_status', 'available') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            @error('availability_status')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.facilities.index') }}" class="fms-btn-secondary">
                Cancel
            </a>
            <button type="submit" class="fms-btn-primary">
                Create Facility
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('owner_type').addEventListener('change', function() {
    const collegeField = document.getElementById('college-field');
    if (this.value === 'college') {
        collegeField.classList.remove('hidden');
    } else {
        collegeField.classList.add('hidden');
    }
});
</script>
@endsection 