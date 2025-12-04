@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold text-blue-700">
                {{ $overtime->id ? 'Edit Overtime Request' : 'Overtime Request Form' }}
            </h1>
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-2 bg-gray-200 text-gray-800 px-5 py-2 rounded-xl hover:bg-gray-300 transition-all font-bold text-lg shadow-md hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- FORM -->
        <form action="{{ $overtime->id ? route('overtime.update', $overtime->id) : route('overtime.store') }}"
            method="POST" class="bg-white shadow border rounded p-6 space-y-6" id="overtimeForm">
            @csrf


            <!-- 2 columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Name -->
                <div>
                    <label class="block font-semibold mb-1">Name</label>
                    <select name="staff_id" id="staff_id"
                        class="w-full border px-2 py-1 rounded focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Staff</option>
                        @foreach ($staffs as $s)
                            <option value="{{ $s->id }}" data-position="{{ $s->position }}"
                                data-branch="{{ $s->branch_id }}" data-department="{{ $s->department_id }}">
                                {{ $s->staff_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Position (Auto-filled) -->
                <div>
                    <label class="block font-semibold mb-1">Position</label>
                    <input type="text" id="position" name="position" class="w-full border px-2 py-1 rounded bg-gray-100"
                        readonly required>
                </div>

                <!-- Branch (Auto-filled) -->
                <div>
                    <label class="block font-semibold mb-1">Branch</label>
                    <select id="branch_id_display" class="w-full border px-2 py-1 rounded bg-gray-100" disabled>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>

                    <!-- Hidden input that gets submitted -->
                    <input type="hidden" name="branch_id" id="branch_id">
                </div>

                <!-- Department (Auto-filled) -->
                <div>
                    <label class="block font-semibold mb-1">Department</label>
                    <select id="department_id_display" class="w-full border px-2 py-1 rounded bg-gray-100" disabled>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>

                    <!-- Hidden input that gets submitted -->
                    <input type="hidden" name="department_id" id="department_id">
                </div>

                <!-- Date -->
                <div>
                    <label class="block font-semibold mb-1">Date</label>
                    <input type="date" name="date"
                        class="w-full border px-2 py-1 rounded focus:ring-2 focus:ring-blue-500"
                        value="{{ old('date', now()->format('Y-m-d')) }}" required>
                </div>

                <!-- Reg No (Only For After Sales Dept) -->
                <div>
                    <label class="block font-semibold mb-1">Reg No (For After Sales Dept Only)</label>
                    <input type="text" name="reg_no"
                        class="w-full border px-2 py-1 rounded focus:ring-2 focus:ring-blue-500"
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
                                class="w-full border px-2 py-1 rounded focus:ring-2 focus:ring-blue-500"
                                value="{{ old('start_time') }}" required>
                        </div>

                        <!-- End Time -->
                        <div>
                            <label class="block font-semibold mb-1">End Time</label>
                            <input type="time" name="end_time" id="end_time"
                                class="w-full border px-2 py-1 rounded focus:ring-2 focus:ring-blue-500"
                                value="{{ old('end_time') }}" required>
                        </div>

                        <!-- Total Hours (Auto-calculated) -->
                        <div>
                            <label class="block font-semibold mb-1">Total Hours</label>
                            <input type="text" name="total_hours" id="total_hours"
                                class="w-full border px-2 py-1 rounded bg-gray-50 font-bold text-blue-700" readonly
                                placeholder="Total Hours will be calculated ">
                        </div>
                    </div>
                </div>

                <!-- Type of Work -->
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-1">Type of Work</label>
                    <textarea name="type_of_work" rows="4" class="w-full border p-3 rounded focus:ring-2 focus:ring-blue-500"
                        required>{{ old('type_of_work') }}</textarea>
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
            document.addEventListener("DOMContentLoaded", function() {
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

                document.getElementById('staff_id').addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];

                    const position = selected.getAttribute('data-position');
                    const branchId = selected.getAttribute('data-branch');
                    const departmentId = selected.getAttribute('data-department');

                    // Position
                    document.getElementById('position').value = position ?? '';

                    // Visible disabled dropdowns
                    document.getElementById('branch_id_display').value = branchId;
                    document.getElementById('department_id_display').value = departmentId;

                    // Hidden inputs for submission
                    document.getElementById('branch_id').value = branchId;
                    document.getElementById('department_id').value = departmentId;
                });
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
    @endsection
