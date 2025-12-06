@extends('layouts.app')

@section('content')

        <!-- HEADER (Matches Overtime View design) -->
        <div class="pt-6 sm:pt-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">

                <!-- Title -->
                <h1 class="text-3xl font-bold text-blue-700 tracking-tight leading-snug">
                    {{ $overtime->id ? 'Edit Overtime Request' : 'Overtime Request Form' }}
                </h1>

                <!-- Back Button -->
                <a href="{{ url()->previous() }}" 
                class="inline-flex items-center justify-center gap-2 bg-gray-200 text-gray-800 px-4 py-2.5 rounded-xl font-bold text-lg shadow-md hover:bg-gray-300 transition-all w-full">
                    <!-- Arrow on the left -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <!-- Text -->
                    <span class="flex-1 text-center">Back to List</span>
                </a>
            </div>
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

                <!-- Position -->
                <div>
                    <label class="block font-semibold mb-1">Position</label>
                    <input type="text" id="position" name="position"
                        class="w-full border px-2 py-1 rounded bg-gray-100" readonly required>
                </div>

                <!-- Branch -->
                <div>
                    <label class="block font-semibold mb-1">Branch</label>
                    <select id="branch_id_display" class="w-full border px-2 py-1 rounded bg-gray-100" disabled>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="branch_id" id="branch_id">
                </div>

                <!-- Department -->
                <div>
                    <label class="block font-semibold mb-1">Department</label>
                    <select id="department_id_display" class="w-full border px-2 py-1 rounded bg-gray-100" disabled>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="department_id" id="department_id">
                </div>

                <!-- Date -->
                <div>
                    <label class="block font-semibold mb-1">Date</label>
                    <input type="date" name="date"
                        class="w-full border px-2 py-1 rounded focus:ring-2 focus:ring-blue-500"
                        value="{{ old('date', now()->format('Y-m-d')) }}" required>
                </div>

                <!-- Reg No -->
                <div>
                    <label class="block font-semibold mb-1">Reg No (For After Sales Dept Only)</label>
                    <input type="text" name="reg_no"
                        class="w-full border px-2 py-1 rounded focus:ring-2 focus:ring-blue-500"
                        value="{{ old('reg_no') }}">
                </div>

                <!-- Overtime Schedule -->
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

                        <!-- Total Hours -->
                        <div>
                            <label class="block font-semibold mb-1">Total Hours</label>
                            <input type="text" name="total_hours" id="total_hours"
                                class="w-full border px-2 py-1 rounded bg-gray-50 font-bold text-blue-700" readonly
                                placeholder="Total Hours will be calculated">
                        </div>
                    </div>
                </div>

                <!-- Type of Work -->
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-1">Type of Work</label>
                    <textarea name="type_of_work" rows="4"
                        class="w-full border p-3 rounded focus:ring-2 focus:ring-blue-500"
                        required>{{ old('type_of_work') }}</textarea>
                </div>

            </div>

            <!-- Submit -->
            <div class="mt-6">
                <button type="submit"
                    class="w-full bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700 transition duration-200">
                    Submit & Generate QR Code
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JS -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Auto calculate total hours
        document.getElementById('overtimeForm').addEventListener('input', function(e) {
            const start = document.getElementById('start_time').value;
            const end = document.getElementById('end_time').value;

            if (start && end) {
                const [sh, sm] = start.split(':').map(Number);
                const [eh, em] = end.split(':').map(Number);

                let diff = (eh * 60 + em) - (sh * 60 + sm);
                if (diff < 0) diff += 1440;

                const hours = Math.floor(diff / 60);
                const mins = diff % 60;

                document.getElementById('total_hours').value = (hours + mins / 60).toFixed(2) + ' hours';
            } else {
                document.getElementById('total_hours').value = '';
            }
        });

        // Auto-fill based on selected staff
        document.getElementById('staff_id').addEventListener('change', function() {
            const s = this.options[this.selectedIndex];

            document.getElementById('position').value = s.dataset.position ?? '';

            document.getElementById('branch_id_display').value = s.dataset.branch;
            document.getElementById('department_id_display').value = s.dataset.department;

            document.getElementById('branch_id').value = s.dataset.branch;
            document.getElementById('department_id').value = s.dataset.department;
        });

    });
</script>

<!-- Reminder -->
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
