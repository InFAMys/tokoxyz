@extends('layouts.app')

@section('title', 'My Account')

@section('content')
    <div class="bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Welcome, {{ auth()->user()->nama }}</h1>
        <p class="text-gray-500 mb-6">This is your customer account area.</p>

        <div class="border rounded-lg p-4">
            <p class="text-sm text-gray-500">Account email</p>
            <p class="text-lg font-medium">{{ auth()->user()->email }}</p>
        </div>
    </div>
@endsection
