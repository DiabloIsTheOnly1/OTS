@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-blue-700 mb-6">Overtime Request Form</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('overtime.store') }}" method="POST" class="bg-white shadow border rounded p-6 space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label class="block font-semibold mb-1">Name</label>
            <input type="text" name="name" class="w-full border p-2 rounded"
                   value="{{ old('name') }}" required>
        </div>

        <!-- Position -->
        <div>
            <label class="block font-semibold mb-1">Position</label>
            <input type="text" name="position" class="w-full border p-2 rounded"
                   value="{{ old('position') }}" required>
        </div>

        <!-- Branch -->
        <div>
            <label class="block font-semibold mb-1">Branch</label>
            <select name="branch_id" class="w-full border p-2 rounded" required>
                <option value="">Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Department -->
        <div>
            <label class="block font-semibold mb-1">Department</label>
            <select name="department_id" class="w-full border p-2 rounded" required>
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Date -->
        <div>
            <label class="block font-semibold mb-1">Date</label>
            <input type="date" name="date" class="w-full border p-2 rounded" required>
        </div>

        <!-- Work Description -->
        <div>
            <label class="block font-semibold mb-1">Work to be completed during OT</label>
            <textarea name="reason" rows="4" class="w-full border p-2 rounded" required>{{ old('reason') }}</textarea>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
            Submit & Generate QR
        </button>
    </form>
</div>

@endsection
