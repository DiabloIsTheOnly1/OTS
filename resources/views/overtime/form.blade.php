@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold text-blue-700 mb-6">Overtime Request Form</h1>

<form action="{{ route('overtime.store') }}" method="POST" class="bg-white shadow rounded p-6 space-y-4">
    @csrf

    <div>
        <label class="block font-medium mb-1">Name</label>
        <input type="text" name="employee_name" required class="w-full border p-2 rounded">
    </div>

    <div>
        <label class="block font-medium mb-1">Department</label>
        <input type="text" name="department" class="w-full border p-2 rounded">
    </div>

    <div>
        <label class="block font-medium mb-1">Date</label>
        <input type="date" name="date" required class="w-full border p-2 rounded">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-medium mb-1">Start Time</label>
            <input type="time" name="start_time" required class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block font-medium mb-1">End Time</label>
            <input type="time" name="end_time" required class="w-full border p-2 rounded">
        </div>
    </div>

    <div>
        <label class="block font-medium mb-1">Reason</label>
        <textarea name="reason" rows="4" class="w-full border p-2 rounded"></textarea>
    </div>

    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full">
        Submit
    </button>

</form>

@endsection
