<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:8|confirmed',
            'role'              => 'required|in:admin,college_staff,org_staff',
            'college_name'      => 'nullable|string|max:255',
            'organization_name' => 'nullable|string|max:255',
        ];
    }
}
