@extends('layouts.app')

@section('content')
    <div class="pt-5 md:pt-8 min-h-screen">

        {{-- Success & Error Messages --}}
        @if (session('success'))
            <div
                class="max-w-4xl mx-auto mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="max-w-4xl mx-auto mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div class="max-w-4xl mx-auto mb-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <h1 class="text-3xl md:text-4xl font-bold text-blue-700 text-center md:text-left leading-tight">
                    {{ $overtime->id ? 'Edit Overtime Request' : 'Overtime Request Form' }}
                </h1>
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-200 hover:bg-gray-400 text-gray-800 rounded-xl font-medium text-sm shadow-md hover:shadow transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Main Form -->
        <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-xl border border-gray-200 py-4 px-8 md:py-4 ">
            <form action="{{ $overtime->id ? route('overtime.update', $overtime->id) : route('overtime.store') }}"
                method="POST" class="space-y-4" id="overtimeForm">
                @csrf
                @if ($overtime->id)
                    @method('PUT')
                @endif

                <!-- Staff & Position -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <select name="staff_id" id="staff_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">Select Staff</option>
                            @foreach ($staffs as $s)
                                <option value="{{ $s->id }}" data-position="{{ $s->position }}"
                                    data-branch="{{ $s->branch_id }}" data-department="{{ $s->department_id }}"
                                    {{ old('staff_id', $overtime->staff_id ?? '') == $s->id ? 'selected' : '' }}>
                                    {{ $s->staff_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
                        <input type="text" id="position" readonly
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-gray-700 font-medium">
                    </div>
                </div>

                <!-- Hidden Fields -->
                <input type="hidden" name="branch_id" id="branch_id">
                <input type="hidden" name="department_id" id="department_id">

                <!-- Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            value="{{ old('date', $overtime->date ?? now()->format('Y-m-d')) }}">
                        @error('date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div></div>
                </div>

                <!-- Overtime Hours Card -->
                <div class="border-t border-gray-300 pt-4">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Overtime Hours</h3>

                    <div class="max-w-md mx-auto bg-gray-50 border border-gray-200 rounded-2xl p-4 shadow-sm">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 text-center">
                            Total Overtime Hours Requested <span class="text-red-500">*</span>
                        </label>

                        @php
                            $total =
                                old('requested_hours_h') !== null
                                    ? old('requested_hours_h') + (old('requested_hours_m') ?? 0) / 60
                                    : $overtime->total_hours ?? 0;

                            $hours = floor($total);
                            $minutes = round(($total - $hours) * 60);
                        @endphp

                        <div class="flex justify-center gap-4 mb-2">
                            <!-- Hours -->
                            <div class="flex flex-col items-center">
                                <input type="number" name="requested_hours_h" id="requested_hours_h" min="0"
                                    max="12"
                                    class="w-24 text-center text-2xl font-bold text-blue-700 border border-gray-300 rounded-xl px-4 py-1 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                                    value="{{ old('requested_hours_h', $hours) }}">
                                <span class="text-sm text-gray-500 mt-1">Hours</span>
                            </div>

                            <!-- Minutes -->
                            <div class="flex flex-col items-center">
                                <input type="number" name="requested_hours_m" id="requested_hours_m" min="0"
                                    max="59" step="5"
                                    class="w-24 text-center text-2xl font-bold text-blue-700 border border-gray-300 rounded-xl px-4 py-1 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                                    value="{{ old('requested_hours_m', $minutes) }}">
                                <span class="text-sm text-gray-500 mt-1">Minutes</span>
                            </div>
                        </div>

                        <p class="text-center text-sm text-gray-500 mt-2">
                            Enter hours and minutes (e.g., 1 hour 30 minutes = 01h 30m)
                        </p>

                        @error('requested_hours_h')
                            <p class="mt-2 text-center text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('requested_hours_m')
                            <p class="mt-2 text-center text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Type of Work -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Reg No (After Sales Dept Only)
                    </label>
                    <input type="text" name="reg_no"
                        class="w-1/2 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition mb-2"
                        value="{{ old('reg_no', $overtime->reg_no ?? '') }}" placeholder="Optional">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Type of Work <span class="text-red-500">*</span>
                    </label>
                    <textarea name="type_of_work" rows="4" required
                        class="w-full border border-gray-300 rounded-lg p-5 text-base leading-relaxed resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Please describe the work to be done in detail...">{{ old('type_of_work', $overtime->type_of_work ?? '') }}</textarea>
                    @error('type_of_work')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit"
                        class="inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-m px-4 py-3 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Submit & Generate QR Code
                    </button>
                </div>
            </form>
        </div>

        <!-- Reminder Box -->
        <div
            class="max-w-4xl mx-auto mt-12 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-10 shadow-xl">
            <h3 class="text-2xl font-bold text-blue-900 mb-6 text-center">
                Reminder
            </h3>
            <ul class="text-gray-700 space-y-4 text-base leading-relaxed max-w-3xl mx-auto">
                <li class="flex items-start gap-3"><span class="text-blue-600 mt-1">•</span> Please submit form <strong>at
                        least 1 hour before</strong> overtime starts.</li>
                <li class="flex items-start gap-3"><span class="text-blue-600 mt-1">•</span> Staff under HQ superiors must
                    get their approval first.</li>
                <li class="flex items-start gap-3"><span class="text-blue-600 mt-1">•</span> Executive & above — please
                    consult HR for allowance eligibility.</li>
                <li class="flex items-start gap-3"><span class="text-blue-600 mt-1">•</span> Approval from immediate
                    superior is <strong>mandatory</strong> before submission.</li>
                <li class="flex items-start gap-3"><span class="text-blue-600 mt-1">•</span> Branch approvers: Max 2
                    hrs/day or 6 hrs/week per staff.</li>
            </ul>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            const staffSelect = $('#staff_id');
            const positionInput = $('#position');
            const branchId = $('#branch_id');
            const deptId = $('#department_id');

            // Init Select2
            staffSelect.select2({
                placeholder: 'Select Staff',
                allowClear: true,
                width: '100%'
            });

            // Autofill on Select2 select
            staffSelect.on('select2:select', function(e) {
                const option = e.params.data.element;

                positionInput.val(option.dataset.position || '');
                branchId.val(option.dataset.branch || '');
                deptId.val(option.dataset.department || '');
            });

            // Clear fields when cleared
            staffSelect.on('select2:clear', function() {
                positionInput.val('');
                branchId.val('');
                deptId.val('');
            });

            // Trigger autofill on page load (EDIT mode)
            if (staffSelect.val()) {
                const option = staffSelect.find('option:selected')[0];

                positionInput.val(option.dataset.position || '');
                branchId.val(option.dataset.branch || '');
                deptId.val(option.dataset.department || '');
            }
        });
    </script>
@endsection
