<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $data = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ];

        if ($validated['role'] === 'college_staff') {
            $data['college_name'] = $validated['college_name'];
        }

        if ($validated['role'] === 'org_staff') {
            $data['organization_name'] = $validated['organization_name'];
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('status', 'User account created successfully.');
    }
}
