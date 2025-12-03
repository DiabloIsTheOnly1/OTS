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
      class="bg-white shadow border rounded p-6 space-y-6" id="overtimeForm">
    @csrf

    <!-- 2 columns -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Name -->
        <div>
            <label class="block font-semibold mb-1">Name</label>
            <input type="text" name="name" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500"
                   value="{{ old('name') }}" required>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Position -->
        <div>
            <label class="block font-semibold mb-1">Position</label>
            <input type="text" name="position" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500"
                   value="{{ old('position') }}" required>
        </div>

        <!-- Branch -->
        <div>
            <label class="block font-semibold mb-1">Branch</label>
            <select name="branch_id" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}"
                        {{ old('branch_id', $selectedBranch ?? '') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Department -->
        <div>
            <label class="block font-semibold mb-1">Department</label>
            <select name="department_id" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}"
                        {{ old('department_id', $selectedDepartment ?? '') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->department_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date -->
        <div>
            <label class="block font-semibold mb-1">Date</label>
            <input type="date" name="date" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500"
                   value="{{ old('date', now()->format('Y-m-d')) }}" required>
        </div>

        <!-- Reg No (Only For After Sales Dept) -->
        <div>   
            <label class="block font-semibold mb-1">Reg No (For After Sales Dept Only)</label>
            <input type="text" name="reg_no" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500"
                   value="{{ old('reg_no') }}">
        </div>

        <!-- Overtime Time Range -->
        <div class="md:col-span-2 border-t pt-4">
            <h3 class="font-semibold text-lg mb-4">Overtime Schedule</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Start Time -->
                <div>
                    <label class="block font-semibold mb-1">Start Time</label>
                    <input type="time" name="start_time" id="start_time"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500"
                           value="{{ old('start_time') }}" required>
                </div>

                <!-- End Time -->
                <div>
                    <label class="block font-semibold mb-1">End Time</label>
                    <input type="time" name="end_time" id="end_time"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500"
                           value="{{ old('end_time') }}" required>
                </div>

                <!-- Total Hours (Auto-calculated) -->
                <div>
                    <label class="block font-semibold mb-1">Total Hours</label>
                    <input type="text" name="total_hours" id="total_hours"
                           class="w-full border p-2 rounded bg-gray-50 font-bold text-blue-700"
                           readonly placeholder="Total Hours will be calculated ">
                </div>
            </div>
        </div>

        <!-- Type of Work -->
        <div class="md:col-span-2">
            <label class="block font-semibold mb-1">Type of Work</label>
            <textarea name="reason" rows="4"
                      class="w-full border p-3 rounded focus:ring-2 focus:ring-blue-500"
                      required>{{ old('reason') }}</textarea>
        </div>

    </div>

    <!-- Submit Button -->
    <div class="mt-6">
        <button type="submit"
                class="w-full bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700 transition duration-200">
            Submit & Generate QR Code
        </button>
    </div>
</form>

<!-- JavaScript to Calculate Total Hours -->
<script>
    document.getElementById('overtimeForm').addEventListener('input', function(e) {
        const start = document.getElementById('start_time').value;
        const end = document.getElementById('end_time').value;

        if (start && end) {
            const [startHour, startMin] = start.split(':').map(Number);
            const [endHour, endMin] = end.split(':').map(Number);

            let diffMins = (endHour * 60 + endMin) - (startHour * 60 + startMin);

            // Handle overnight overtime (e.g., 22:00 to 06:00)
            if (diffMins < 0) {
                diffMins += 24 * 60;
            }

            const hours = Math.floor(diffMins / 60);
            const mins = diffMins % 60;

            const totalHours = hours + (mins / 60);
            document.getElementById('total_hours').value = totalHours.toFixed(2) + ' hours';
        } else {
            document.getElementById('total_hours').value = '';
        }
    });
</script>

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
