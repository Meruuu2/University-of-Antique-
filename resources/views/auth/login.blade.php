@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md border border-black bg-white p-6">
        <h1 class="mb-4 text-xl font-semibold text-black">Sign in</h1>

        @if ($errors->any())
            <div class="fms-alert-error mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-4">
                <label class="fms-label" for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="fms-input"
                >
            </div>
            <div class="mb-4">
                <label class="fms-label" for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="fms-input"
                >
            </div>

            <div class="flex justify-end">
                <button type="submit" class="fms-btn-primary">
                    Login
                </button>
            </div>
        </form>
    </div>
@endsection
 