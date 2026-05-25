@extends('layouts.admin')

@section('admin-content')
<div class="bg-white rounded shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Add New User
        </a>
    </div>

    @if(session('status'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 border border-green-200 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    </tr>
                </thead> 
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-4 py-2">{{ $user->name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($user->role === 'admin') bg-purple-100 text-purple-800
                                    @elseif($user->role === 'college_staff') bg-blue-100 text-blue-800
                                    @elseif($user->role === 'org_staff') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                @if($user->college_name) {{ $user->college_name }}
                                @elseif($user->organization_name) {{ $user->organization_name }}
                                @else - @endif
                            </td>
                            <td class="px-4 py-2">{{ $user->created_at?->format('Y-m-d') ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @else
        <p class="text-gray-600">No users found.</p>
    @endif
</div>
@endsection
