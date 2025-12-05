@extends('layouts.app')

@section('content')
    <div class="max-w-full mx-auto space-y-8 py-8 px-4 sm:px-2 lg:px-2 relative">

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

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-700">Overtime Request Details</h1>

            <div class="flex items-center gap-4">
                <button type="button" onclick="window.location.href='{{ route('hr.dashboard') }}'"
                    class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2.5 rounded-xl font-bold text-sm shadow transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </button>

                @canAccess('manage_request')
                @if (!$overtime->clocks()->exists())
                    <button type="button" id="edit-btn"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-bold shadow-lg transition-all duration-200">
                        Edit
                    </button>
                @else
                    <button type="button" disabled title="Cannot edit after clock-in"
                        class="bg-gray-400 text-gray-200 px-6 py-2 rounded-xl font-bold cursor-not-allowed opacity-75 shadow">
                        Edit Disabled
                    </button>
                @endif
                @endcanAccess

                <div id="save-cancel-btns" class="hidden items-center gap-3">
                    <button type="submit" form="edit-form"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg transition-all duration-200">
                        Save Changes
                    </button>
                    <button type="button" id="cancel-btn"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-xl font-bold shadow transition-all duration-200">
                        Cancel
                    </button>
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

                            <!-- VIEW MODE — just the name -->
                            <div class="view-mode font-medium text-lg">
                                {{ $overtime->staff->staff_name ?? 'N/A' }}
                            </div>

               
                            <select name="staff_id"
                                class="edit-mode hidden w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                required>
                                @foreach (\App\Models\Staff::orderBy('staff_name')->get() as $s)
                                    <option value="{{ $s->id }}"
                                        {{ $s->id == $overtime->staff_id ? 'selected' : '' }}>
                                        {{ $s->staff_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- POSITION -->
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

                        <!-- Empty spacer to keep layout balanced -->
                        <div></div>

                        <!-- PLANNED OVERTIME SCHEDULE -->
                        <div class="md:col-span-2 bg-amber-50 border border-amber-300 rounded-lg p-5">
                            <h3 class="font-bold text-amber-900 text-lg mb-2">Planned Overtime Schedule</h3>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-amber-700 font-medium uppercase tracking-wider">Start</p>
                                    <p class="view-mode text-lg font-bold text-amber-800">
                                        {{ $overtime->start_time?->format('H:i') ?? '-' }}</p>
                                    <input type="time" name="start_time"
                                        class="edit-mode hidden w-full text-center text-lg font-bold text-amber-800 border border-amber-400 rounded px-2 py-1"
                                        value="{{ old('start_time', $overtime->start_time?->format('H:i')) }}" required>
                                </div>
                                <div class="flex items-center justify-center">
                                    <span class="text-3xl text-amber-600">→</span>
                                </div>
                                <div>
                                    <p class="text-xs text-amber-700 font-medium uppercase tracking-wider">End</p>
                                    <p class="view-mode text-lg font-bold text-amber-800">
                                        {{ $overtime->end_time?->format('H:i') ?? '-' }}</p>
                                    <input type="time" name="end_time"
                                        class="edit-mode hidden w-full text-center text-lg font-bold text-amber-800 border border-amber-400 rounded px-2 py-1"
                                        value="{{ old('end_time', $overtime->end_time?->format('H:i')) }}" required>
                                </div>
                            </div>

                            <div class="text-center mt-2">
                                <div id="total-hours-display"
                                    class="inline-block bg-amber-700 text-white px-4 py-1 rounded-full font-bold text-md">
                                    @if ($overtime->total_hours > 0)
                                        @php
                                            $h = floor($overtime->total_hours);
                                            $m = round(($overtime->total_hours - $h) * 60);
                                        @endphp
                                        {{ $h }}h {{ $m > 0 ? $m . 'm' : '' }}
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
                            <div
                                class="view-mode bg-gray-50 border border-gray-300 rounded-lg p-4 min-h-32 flex items-start leading-relaxed">
                                {{ $overtime->type_of_work ?: 'No details provided' }}
                            </div>
                            <textarea name="type_of_work" rows="4"
                                class="edit-mode hidden w-full border border-gray-300 rounded-lg p-4 resize-none focus:ring-2 focus:ring-blue-500">
                                {{ old('type_of_work', $overtime->type_of_work) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Clock Records + Remarks -->
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
                                <div class="bg-white border rounded-lg px-4 py-1 shadow-sm hover:shadow transition-shadow">
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm flex space-x-6">
                                            <div><span class="font-semibold">In:</span>
                                                {{ $session->clock_in->format('H:i') }}</div>
                                            <div><span class="font-semibold">Out:</span>
                                                {{ $session->clock_out?->format('H:i') ?? '—' }}</div>
                                        </div>
                                        <span class="bg-blue-100 text-blue-700 font-semibold px-2 py-1 rounded-full text-sm">
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
                            value="{{ $overtime->approver?->name ?? ($overtime->approver?->username ?? 'Pending') }}"
                            readonly>
                    </div>

                    <div>
                    <label class="block font-semibold text-gray-600 mb-1">Remarks</label>

                    <!-- VIEW MODE -->
                    <div class="view-mode bg-gray-50 border border-gray-300 rounded-lg p-4 min-h-32 flex items-start leading-relaxed">
                        {{ $overtime->remarks ?: '—' }}
                    </div>

                    <div class="edit-mode hidden">
                        <textarea 
                            name="remarks" 
                            rows="4" 
                            class="w-full border border-gray-300 rounded-lg p-4 resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Add any remarks...">{{ old('remarks', $overtime->remarks) }}</textarea>
                    </div>

                </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Toast Animation + Auto Hide -->
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slideIn 0.4s ease-out;
        }
    </style>

    <script>
        // Auto-hide toasts
        setTimeout(() => {
            document.querySelectorAll('#toast-success, #toast-error').forEach(toast => {
                if (toast) toast.style.opacity = '0';
                setTimeout(() => toast?.remove(), 500);
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
    </script>
@endsection
