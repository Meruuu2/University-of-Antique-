@extends('layouts.admin')

@section('admin-content')
<div class="bg-white rounded shadow p-6">
    <h1 class="text-2xl font-semibold mb-4">Add New User</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 text-red-800 border border-red-200 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                       value="{{ old('name') }}">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                       value="{{ old('email') }}">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required minlength="8"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
            </div>
            <div> 
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
            </div>
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" id="role" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                <option value="">Select Role</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="college_staff" {{ old('role') === 'college_staff' ? 'selected' : '' }}>College Staff</option>
                <option value="org_staff" {{ old('role') === 'org_staff' ? 'selected' : '' }}>Organization Staff</option>
            </select>
        </div>

        <div id="college-field" class="hidden">
            <label for="college_name" class="block text-sm font-medium text-gray-700">College Name</label>
            <input type="text" name="college_name" id="college_name"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                   value="{{ old('college_name') }}">
        </div>

        <div id="org-field" class="hidden">
            <label for="organization_name" class="block text-sm font-medium text-gray-700">Organization Name</label>
            <input type="text" name="organization_name" id="organization_name"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                   value="{{ old('organization_name') }}">
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Create User
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const collegeField = document.getElementById('college-field');
    const orgField = document.getElementById('org-field');
    
    if (this.value === 'college_staff') {
        collegeField.classList.remove('hidden');
        orgField.classList.add('hidden');
    } else if (this.value === 'org_staff') {
        collegeField.classList.add('hidden');
        orgField.classList.remove('hidden');
    } else {
        collegeField.classList.add('hidden');
        orgField.classList.add('hidden');
    }
});
</script>
@endsection
