@extends('layouts.app')

@section('content')
<div class="pt-20 md:pt-8 min-h-screen bg-gray-50">

    {{-- SUCCESS & ERROR MESSAGES --}}
    @if (session('success'))
        <div class="max-w-4xl mx-auto mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-4xl mx-auto mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- HEADER — Clean & Corporate -->
    <div class="max-w-4xl mx-auto mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <h1 class="text-3xl md:text-4xl font-bold text-blue-700 text-center md:text-left leading-tight">
                {{ $overtime->id ? 'Edit Overtime Request' : 'Overtime Request Form' }}
            </h1>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium text-sm shadow-sm hover:shadow transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Back to List</span>
            </a>
        </div>
    </div>

    
    <!-- MAIN FORM — Your original, just polished -->
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-xl border border-gray-200 p-8 md:p-10">
        <form action="{{ $overtime->id ? route('overtime.update', $overtime->id) : route('overtime.store') }}"
              method="POST" class="space-y-8" id="overtimeForm">
            @csrf
            @if($overtime->id) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                    <select name="staff_id" id="staff_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">Select Staff</option>
                        @foreach ($staffs as $s)
                            <option value="{{ $s->id }}"
                                data-position="{{ $s->position }}"
                                data-branch="{{ $s->branch_id }}"
                                data-department="{{ $s->department_id }}"
                                {{ old('staff_id', $overtime->staff_id ?? '') == $s->id ? 'selected' : '' }}>
                                {{ $s->staff_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Position -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
                    <input type="text" id="position" readonly
                           class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="branch_id" id="branch_id">
                <input type="hidden" name="department_id" id="department_id">

                <!-- Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="date" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           value="{{ old('date', $overtime->date ?? now()->format('Y-m-d')) }}">
                </div>

                
             
                </div>

                <!-- Overtime Schedule -->
                <div class="md:col-span-2 border-t-2 border-gray-200 pt-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 text-center">Overtime Schedule</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                        <!-- Start Time -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Start Time</label>
                            <input type="time" name="start_time" id="start_time" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 text-center text-lg font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   value="{{ old('start_time') }}">
                        </div>

                        <!-- End Time -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">End Time</label>
                            <input type="time" name="end_time" id="end_time" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 text-center text-lg font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   value="{{ old('end_time') }}">
                        </div>

                        <!-- Total Hours -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Total Hours</label>
                            <input type="text" id="total_hours"
                                   class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-center text-lg font-bold text-blue-700"
                                   value="" readonly placeholder="Calculated automatically">
                        </div>
                    </div>
                </div>

                <!-- Reg No -->
                   <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Reg No (For After Sales Dept Only)</label>
                    <input type="text" name="reg_no"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           value="{{ old('reg_no') }}" placeholder="Optional">
                   </div>

                <!-- Type of Work -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type of Work <span class="text-red-500">*</span></label>
                    <textarea name="type_of_work" rows="6" required
                              class="w-full border border-gray-300 rounded-lg p-5 text-base leading-relaxed resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                              placeholder="Please describe the work to be done...">{{ old('type_of_work') }}</textarea>
                </div>

            

            <!-- Submit Button -->
            <div class="text-center pt-8">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg px-16 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                    Submit & Generate QR Code
                </button>
            </div>
        </form>
    </div>
    </div>

    <!-- Reminder Box -->
    <div class="max-w-4xl mx-auto mt-12 bg-blue-50 border border-blue-200 rounded-xl p-8 shadow-md">
        <h3 class="text-xl font-bold text-blue-900 mb-6 text-center">Reminder</h3>
        <ul class="text-gray-700 space-y-3 text-base leading-relaxed">
            <li>• Please ensure to submit form 1 hour before.</li>
            <li>• Those immediate superior in HQ, do seek their approval too.</li>
            <li>• Exec & above may refer HR team for allowance.</li>
            <li>• Approval from superior is required before submission.</li>
            <li>• Branches with approval authority may approve max 2 hrs/day or 6 hrs/week.</li>
        </ul>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const staffSelect = document.getElementById('staff_id');
        const positionInput = document.getElementById('position');
        const branchId = document.getElementById('branch_id');
        const deptId = document.getElementById('department_id');
        const totalHoursInput = document.getElementById('total_hours');

        // Auto-fill staff details
        staffSelect?.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            positionInput.value = option.dataset.position || '';
            branchId.value = option.dataset.branch || '';
            deptId.value = option.dataset.department || '';
        });

        // Calculate total hours
        function calculateHours() {
            const start = document.getElementById('start_time').value;
            const end = document.getElementById('end_time').value;

            if (start && end) {
                const [sh, sm] = start.split(':').map(Number);
                const [eh, em] = end.split(':').map(Number);
                let diff = (eh * 60 + em) - (sh * 60 + sm);
                if (diff < 0) diff += 1440;
                const hours = (diff / 60).toFixed(2);
                totalHoursInput.value = hours + ' hours';
            } else {
                totalHoursInput.value = '';
            }
        }

        document.getElementById('start_time')?.addEventListener('change', calculateHours);
        document.getElementById('end_time')?.addEventListener('change', calculateHours);

        // Trigger on load if values exist
        calculateHours();
    });
</script>
@endsection