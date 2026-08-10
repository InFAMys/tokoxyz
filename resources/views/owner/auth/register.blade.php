@extends('layouts.app')

@section('title', 'Admin Register')

@section('content')
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Create Admin account</h1>

        <form method="POST" action="{{ route('owner.register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
                Register
            </button>
        </form>

        <p class="text-sm text-gray-500 mt-6">
            Already have an Admin account?
            <a href="{{ route('owner.login') }}" class="text-indigo-600 hover:underline">Log in</a>
        </p>
    </div>
@endsection
