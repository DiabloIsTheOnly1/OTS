@extends('layouts.app')

@section('content')
<div class="pt-20 sm:pt-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- SUCCESS TOAST --}}
    @if (session('success'))
        <div id="toast-success" class="fixed top-4 right-4 z-50 animate-slide-in">
            <div class="bg-green-600 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- ERROR TOAST --}}
    @if (session('error'))
        <div id="toast-error" class="fixed top-4 right-4 z-50 animate-slide-in">
            <div class="bg-red-600 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- HEADER: Title + Buttons -->
    <div class="mb-10 text-center sm:text-left">

        <!-- Title -->
        <h1 class="text-3xl sm:text-4xl font-bold text-blue-700 mb-6">
            Overtime Request Details
        </h1>

        <!-- Buttons: Mobile = stacked & centered, Desktop = side by side on right -->
        <div class="flex flex-col sm:flex-row sm:justify-end gap-4">

            <!-- Back Button -->
            <a href="{{ route('hr.dashboard') }}"
               class="w-full sm:w-auto inline-flex justify-center items-center gap-3 px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-2xl font-semibold text-base shadow-md hover:shadow-lg transition-all duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Back to List</span>
            </a>

            <!-- Edit / Save-Cancel Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">

                @canAccess('manage_request')
                    @if (!$overtime->clocks()->exists())
                        <button type="button" id="edit-btn"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-10 py-4 rounded-2xl font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit Request</span>
                        </button>
                    @else
                        <button disabled class="w-full bg-gray-400 text-gray-200 px-10 py-4 rounded-2xl font-bold text-base cursor-not-allowed opacity-80 flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Edit Locked</span>
                        </button>
                    @endif
                @endcanAccess

                <!-- Save & Cancel (Hidden by default) -->
                <div id="save-cancel-btns" class="hidden flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                    <button type="submit" form="edit-form"
                            class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-10 py-4 rounded-2xl font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                    <button type="button" id="cancel-btn"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white px-10 py-4 rounded-2xl font-bold text-base shadow-lg transition-all duration-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN FORM -->
    <form id="edit-form" action="{{ route('overtime.update', $overtime->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- LEFT: Employee Details -->
            <div class="bg-white shadow border rounded-lg p-6 space-y-6">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-3">Employee & Request Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Name -->
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Name</label>
                        <div class="view-mode font-medium text-lg">
                            {{ $overtime->staff->staff_name ?? 'N/A' }}
                        </div>
                        <select name="staff_id" class="edit-mode hidden w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                            @foreach (\App\Models\Staff::orderBy('staff_name')->get() as $s)
                                <option value="{{ $s->id }}" {{ $s->id == $overtime->staff_id ? 'selected' : '' }}>
                                    {{ $s->staff_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Position</label>
                        <input type="text" value="{{ $overtime->staff->position ?? '-' }}"
                            class="w-full border px-3 py-2 rounded bg-gray-100" readonly>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Date</label>
                        <div class="view-mode">{{ $overtime->date->format('d M Y (D)') }}</div>
                        <input type="date" name="date" class="edit-mode hidden w-full border rounded px-3 py-2"
                            value="{{ old('date', $overtime->date->format('Y-m-d')) }}" required>
                    </div>

                    <!-- Branch -->
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Branch</label>
                        <input type="text" value="{{ $overtime->branch?->name ?? '-' }}"
                            class="w-full border px-3 py-2 rounded bg-gray-100" readonly>
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Department</label>
                        <input type="text" value="{{ $overtime->department?->department_name ?? '-' }}"
                            class="w-full border px-3 py-2 rounded bg-gray-100" readonly>
                    </div>

                    <div></div> <!-- Spacer -->

                    <!-- Planned Overtime Schedule -->
                    <div class="md:col-span-2 bg-amber-50 border border-amber-300 rounded-lg p-5">
                        <h3 class="font-bold text-amber-900 text-lg mb-lg mb-4 text-center">Planned Overtime Schedule</h3>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-xs text-amber-700 font-medium uppercase">Start</p>
                                <p class="view-mode text-2xl font-bold text-amber-800 mt-2">
                                    {{ $overtime->start_time?->format('H:i') ?? '-' }}
                                </p>
                                <input type="time" name="start_time"
                                    class="edit-mode hidden w-full text-center text-xl font-bold text-amber-800 border border-amber-400 rounded px-3 py-2"
                                    value="{{ old('start_time', $overtime->start_time?->format('H:i')) }}" required>
                            </div>
                            <div class="flex items-center justify-center">
                                <span class="text-4xl text-amber-600 font-light">→</span>
                            </div>
                            <div>
                                <p class="text-xs text-amber-700 font-medium uppercase">End</p>
                                <p class="view-mode text-2xl font-bold text-amber-800 mt-2">
                                    {{ $overtime->end_time?->format('H:i') ?? '-' }}
                                </p>
                                <input type="time" name="end_time"
                                    class="edit-mode hidden w-full text-center text-xl font-bold text-amber-800 border border-amber-400 rounded px-3 py-2"
                                    value="{{ old('end_time', $overtime->end_time?->format('H:i')) }}" required>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <div id="total-hours-display" class="inline-block bg-amber-700 text-white px-6 py-2 rounded-full font-bold text-lg">
                                @if ($overtime->total_hours > 0)
                                    @php
                                        $h = floor($overtime->total_hours);
                                        $m = round(($overtime->total_hours - $h) * 60);
                                    @endphp
                                    {{ $h }}h {{ $m > 0 ? $m.'m' : '' }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Reg No -->
                    <div class="md:col-span-2">
                        <label class="block font-semibold text-gray-600 mb-1">Reg No</label>
                        <div class="view-mode text-lg font-mono font-bold text-blue-700">
                            {{ $overtime->reg_no ?? ($overtime->staff->reg_no ?? '—') }}
                        </div>
                        <input type="text" name="reg_no"
                            class="edit-mode hidden w-full border rounded px-3 py-2 font-mono"
                            value="{{ old('reg_no', $overtime->reg_no ?? $overtime->staff->reg_no) }}">
                    </div>

                    <!-- Type of Work -->
                    <div class="md:col-span-2">
                        <label class="block font-semibold text-gray-700 mb-1">Type of Work</label>
                        <div class="view-mode bg-gray-50 border border-gray-300 rounded-lg p-4 min-h-32 flex items-start leading-relaxed">
                            {{ $overtime->type_of_work ?: 'No details provided' }}
                        </div>
                        <div class="edit-mode hidden mt-2">
                            <textarea name="type_of_work" rows="5"
                                class="w-full border border-gray-300 rounded-lg p-4 resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Describe the type of work...">{{ old('type_of_work', $overtime->type_of_work) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Clock Records + Remarks -->
            <div class="bg-white shadow border rounded-lg p-6 space-y-6">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-3">Actual Clock Records</h2>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <p class="text-sm text-blue-600 font-semibold uppercase tracking-wider">Total Actual OT Hours</p>
                    <p class="text-3xl font-bold text-blue-700 mt-1">
                        {{ $overtime->total_hm ?? '00:00' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase font-bold text-gray-500 tracking-wider mb-3">Clock In/Out Sessions</p>
                    <div class="space-y-2">
                        @forelse ($overtime->clocks as $session)
                            <div class="bg-white border rounded-lg px-4 py-3 shadow-sm hover:shadow transition">
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex space-x-6">
                                        <div><span class="font-medium">In:</span> {{ $session->clock_in->format('H:i') }}</div>
                                        <div><span class="font-medium">Out:</span> {{ $session->clock_out?->format('H:i') ?? '—' }}</div>
                                    </div>
                                    <span class="bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-full text-sm">
                                        {{ $session->total_hm }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-8 italic">No clock records yet</p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Approved / Handled By</label>
                    <input type="text" class="w-full border px-3 py-2 rounded bg-gray-100"
                        value="{{ $overtime->approver?->name ?? ($overtime->approver?->username ?? 'Pending') }}" readonly>
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Remarks</label>
                    <div class="view-mode bg-gray-50 border border-gray-300 rounded-lg p-4 min-h-32 flex items-start leading-relaxed">
                        {{ $overtime->remarks ?: '—' }}
                    </div>
                    <div class="edit-mode hidden mt-2">
                        <textarea name="remarks" rows="4"
                            class="w-full border border-gray-300 rounded-lg p-4 resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Add any remarks...">{{ old('remarks', $overtime->remarks) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
</div>

<style>
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
</style>

<script>
    // Auto-hide toasts
    setTimeout(() => {
        document.querySelectorAll('#toast-success, #toast-error').forEach(toast => {
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }
        });
    }, 4000);

    // Edit mode toggle
    document.getElementById('edit-btn')?.addEventListener('click', function() {
        document.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
        this.classList.add('hidden');
        document.getElementById('save-cancel-btns').classList.remove('hidden');
        calculateTotalHours();
    });

    document.getElementById('cancel-btn')?.addEventListener('click', function() {
        location.reload();
    });

    function calculateTotalHours() {
        const start = document.querySelector('input[name="start_time"]');
        const end = document.querySelector('input[name="end_time"]');
        const display = document.getElementById('total-hours-display');

        function update() {
            if (!start?.value || !end?.value) {
                display.textContent = '—';
                return;
            }
            let [sh, sm] = start.value.split(':').map(Number);
            let [eh, em] = end.value.split(':').map(Number);
            let startMin = sh * 60 + sm;
            let endMin = eh * 60 + em;
            if (endMin < startMin) endMin += 1440;
            let total = endMin - startMin;
            let h = Math.floor(total / 60);
            let m = total % 60;
            display.textContent = h + 'h' + (m > 0 ? ' ' + m + 'm' : '');
        }

        if (start && end) {
            start.addEventListener('change', update);
            end.addEventListener('change', update);
            update();
        }
    }

        document.getElementById('edit-btn')?.addEventListener('click', function() {
        document.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
        this.classList.add('hidden');
        document.getElementById('save-cancel-btns').classList.remove('hidden');

        // 🟦 FIX FOR MOBILE — FORCE SET INPUT VALUES
        let startInput = document.querySelector('input[name="start_time"]');
        let endInput = document.querySelector('input[name="end_time"]');

        if (startInput && !startInput.value) {
            startInput.value = startInput.getAttribute('value');
        }
        if (endInput && !endInput.value) {
            endInput.value = endInput.getAttribute('value');
        }

        calculateTotalHours();
    });
</script>
@endsection