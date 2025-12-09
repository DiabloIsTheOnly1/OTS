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

<!-- HEADER: Title + Back + Edit/Save Buttons on SAME LINE (desktop) -->
<div class="mb-12">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

        <!-- Title — Centered on mobile, left on desktop -->
        <h1 class="text-3xl md:text-4xl font-bold text-blue-700 text-center md:text-left leading-tight">
            Overtime Request Details
        </h1>

        <!-- Buttons Group — Right-aligned on desktop, centered on mobile -->
        <div class="flex flex-col sm:flex-row justify-center md:justify-end gap-3">

            <!-- Back Button -->
            <a href="{{ route('hr.dashboard') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium text-sm shadow-sm hover:shadow transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Back to List</span>
            </a>

            <!-- Edit / Save-Cancel Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">

                @canAccess('manage_request')
                    @if (!$overtime->clocks()->exists())
                        <button type="button" id="edit-btn"
                                class="inline-flex items-center gap-2 px-7 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-medium text-sm shadow-md hover:shadow-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit</span>
                        </button>
                    @else
                        <button disabled class="inline-flex items-center gap-2 px-7 py-3 bg-gray-400 text-gray-200 rounded-xl font-medium text-sm cursor-not-allowed opacity-80">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Locked</span>
                        </button>
                    @endif
                @endcanAccess

                <!-- Save & Cancel — Hidden by default -->
                <div id="save-cancel-btns" class="hidden flex flex-col sm:flex-row gap-3">
                    <button type="submit" form="edit-form"
                            class="inline-flex items-center gap-2 px-7 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl font-medium text-sm shadow-md hover:shadow-lg transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Save Changes</span>
                    </button>
                    <button type="button" id="cancel-btn"
                            class="inline-flex items-center gap-2 px-7 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl font-medium text-sm shadow-md transition-all duration-200">
                        Cancel
                    </button>
                </div>
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

                <!-- TOTAL HOURS REQUESTED — 30% SMALLER -->
                <div class="md:col-span-2 bg-gradient-to-br from-indigo-50 to-blue-50 border-2 border-indigo-300 rounded-xl p-4 text-center shadow-inner">
                    <h3 class="text-lg font-bold text-indigo-900 mb-3">Total Hours Requested</h3>

                    <div class="max-w-xs mx-auto">
                        
                        <!-- View Mode -->
                        <div class="view-mode">
                            <div class="inline-block bg-indigo-700 text-white px-6 py-4 rounded-xl font-bold text-3xl shadow-lg">
                                @if ($overtime->total_hours > 0)
                                    @php
                                        $h = floor($overtime->total_hours);
                                        $m = round(($overtime->total_hours - $h) * 60);
                                    @endphp
                                    {{ $h }}<small class="text-xl opacity-90">h</small>
                                    @if($m > 0)
                                        <span class="ml-2">{{ $m }}<small class="text-xl opacity-90">m</small></span>
                                    @endif
                                @else
                                    <span class="text-indigo-300">—</span>
                                @endif
                            </div>
                        </div>

                        <!-- Edit Mode -->
                        <div class="edit-mode hidden">
                            <input type="number" 
                                name="total_hours" 
                                step="0.25" 
                                min="0.25" 
                                max="24" 
                                required
                                class="w-full text-center text-3xl font-bold text-indigo-700 bg-white border-4 border-indigo-400 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-200 focus:border-indigo-600 outline-none transition-all"
                                value="{{ old('total_hours', $overtime->total_hours) }}"
                                placeholder="4.5">
                            <p class="mt-2 text-xs text-gray-600">Enter in decimal: 4.5 = 4 hours 30 minutes</p>
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