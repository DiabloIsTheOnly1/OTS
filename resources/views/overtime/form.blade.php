@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">
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

    <!-- FORM -->
    <form action="{{ route('overtime.store') }}" method="POST"
          class="bg-white shadow border rounded p-6 space-y-4">
        @csrf

        <!-- 2 columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Name -->
            <div>
                <label class="block font-semibold mb-1">Name</label>
                <input type="text" name="name" class="w-full border p-1 rounded"
                       value="{{ old('name') }}" required>
            </div>

            <!-- Position -->
            <div>
                <label class="block font-semibold mb-1">Position</label>
                <input type="text" name="position" class="w-full border p-1 rounded"
                       value="{{ old('position') }}" required>
            </div>

            <!-- Branch -->
            <div>
                <label class="block font-semibold mb-1">Branch</label>
                <select name="branch_id" class="w-full border p-1 rounded" required>
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <<option value="{{ $branch->id }}"
                        {{ ($selectedBranch == $branch->id) ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Department -->
            <div>
                <label class="block font-semibold mb-1">Department</label>
                <select name="department_id" class="w-full border p-1 rounded" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <<option value="{{ $dept->id }}"
                        {{ ($selectedDepartment == $dept->id) ? 'selected' : '' }}>
                        {{ $dept->department_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Date -->
            <div>
                <label class="block font-semibold mb-1">Date</label>
                <input type="date" name="date" class="w-full border p-1 rounded"
                       value="{{ old('date', now()->toDateString()) }}" required>
            </div>

            <!-- Work Description (full width) -->
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Work to be completed during OT</label>
                <textarea name="reason" rows="4" class="w-full border p-1 rounded" required>{{ old('reason') }}</textarea>
            </div>
        </div>

        <!-- Submit -->
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
            Submit & Generate QR
        </button>
    </form>

    <!-- REMINDER BOX -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-5 rounded shadow-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-2">Reminder</h3>
        <ul class="text-gray-700 list-disc list-inside space-y-1">
            <li>Please ensure to submit form 1 hour before.</li>
            <li>Those immediate superior in HQ, do seek their approval too.</li>
            <li>Exec & above may refer HR team for allowance.</li>
            <li>Approval from superior is required before submission.</li>
            <li>Branches with approval authority may approve max 2 hrs/day or 6 hrs/week.</li>
        </ul>
    </div>

</div>

@endsection
