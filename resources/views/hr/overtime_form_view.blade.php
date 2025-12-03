@extends('layouts.app')

@section('content')

<div class="max-w-full mx-auto space-y-8 py-8 px-4 sm:px-6 lg:px-8">

    <h1 class="text-3xl font-bold text-blue-700 mb-8 text-center md:text-left">
        Overtime Request Details
    </h1>

    <!-- TWO BLOCKS SIDE-BY-SIDE ON DESKTOP -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- BLOCK 1: Employee & Request Info -->
        <div class="bg-white shadow border rounded-lg p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-3">Employee & Request Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Name</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50" 
                           value="{{ $overtime->staff->staff_name ?? 'N/A' }}" readonly>
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Position</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50" 
                           value="{{ $overtime->staff->position ?? 'N/A' }}" readonly>
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Date</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50" 
                           value="{{ $overtime->date->format('d M Y (D)') }}" readonly>
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Branch</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50" 
                           value="{{ $overtime->branch?->name ?? '-' }}" readonly>
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Department</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50" 
                           value="{{ $overtime->department?->department_name ?? '-' }}" readonly>
                </div>

                <!-- COMPACT PLANNED OVERTIME SCHEDULE -->
                <div class="md:col-span-2 bg-amber-50 border border-amber-300 rounded-lg p-5">
                    <h3 class="font-bold text-amber-900 text-lg mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Planned Overtime Schedule
                    </h3>

                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-xs text-amber-700 font-medium uppercase tracking-wider">Start</p>
                            <p class="text-2xl font-bold text-amber-800">{{ $overtime->start_time?->format('H:i') ?? '-' }}</p>
                        </div>
                        <div class="flex items-center justify-center">
                            <span class="text-3xl text-amber-600">→</span>
                        </div>
                        <div>
                            <p class="text-xs text-amber-700 font-medium uppercase tracking-wider">End</p>
                            <p class="text-2xl font-bold text-amber-800">{{ $overtime->end_time?->format('H:i') ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Compact Total Hours -->
                    <div class="mt-4 text-center">
                        <div class="inline-block bg-amber-700 text-white px-8 py-3 rounded-full font-bold text-2xl">
                            @if($overtime->total_hours && $overtime->total_hours > 0)
                                @php
                                    $hours   = floor($overtime->total_hours);
                                    $minutes = round(($overtime->total_hours - $hours) * 60);
                                @endphp
                                {{ $hours }}h {{ $minutes > 0 ? $minutes . 'm' : '' }}
                            @else
                                <span class="text-amber-200">—</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- REG NO + TYPE OF WORK (Reg No on top, Type of Work FULL WIDTH) -->
                <div class="md:col-span-2 space-y-6">

                    <!-- Reg No -->
                    <div class="space-y-2">
                        <label class="block font-semibold text-gray-700">Reg No</label>
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold font-mono tracking-widest text-blue-800">
                                {{ $overtime->staff->reg_no ?? '—' }}
                            </p>
                        </div>
                    </div>

                    <!-- Type of Work – FULL WIDTH & SPACIOUS -->
                    <div class="space-y-2">
                        <label class="block font-semibold text-gray-700 text-lg">Type of Work</label>
                        <textarea rows="5" class="w-full border border-gray-300 p-5 rounded-lg bg-gray-50 text-sm resize-none font-medium focus:ring-2 focus:ring-blue-400" readonly>
{{ trim($overtime->type_of_work) ?: 'No details provided' }}</textarea>
                    </div>
                </div>

            </div>
        </div>

        <!-- BLOCK 2: Clock Sessions + Summary -->
        <div class="bg-white shadow border rounded-lg p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-3">Actual Clock Records</h2>

            <!-- Actual Total Hours -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                <p class="text-sm text-blue-600 font-semibold uppercase tracking-wider">Total Actual OT Hours</p>
                <p class="text-6xl font-bold text-blue-700 mt-2">
                    {{ $overtime->total_hm ?? '00:00' }}
                </p>
                @if($overtime->total_hours && $overtime->clocks->sum('total_time_taken') > 0)
                    @php
                        $actualSeconds = $overtime->clocks->sum('total_time_taken');
                        $plannedMinutes = $overtime->total_hours * 60;
                        $actualMinutes = $actualSeconds / 60;
                        $diff = $actualMinutes - $plannedMinutes;
                    @endphp
                    <p class="text-lg mt-4 font-medium {{ $diff > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $diff > 0 ? '+' : '' }}{{ round($diff) }} min 
                        {{ $diff > 0 ? 'over' : 'under' }} planned
                    </p>
                @endif
            </div>

            <!-- Clock Sessions List -->
            <div>
                <p class="text-xs uppercase font-bold text-gray-500 tracking-wider mb-3">Clock In/Out Sessions</p>
                <div class="space-y-3 max-h-96 overflow-y-auto border rounded-lg p-4 bg-gray-50">
                    @forelse ($overtime->clocks as $session)
                        <div class="bg-white border rounded-lg p-4 shadow-sm hover:shadow transition-shadow">
                            <div class="flex justify-between items-center">
                                <div class="text-sm">
                                    <div><span class="font-semibold">In:</span> {{ $session->clock_in->format('H:i') }}</div>
                                    <div><span class="font-semibold">Out:</span> {{ $session->clock_out?->format('H:i') ?? '—' }}</div>
                                </div>
                                <span class="bg-blue-100 text-blue-700 font-bold px-4 py-2 rounded-full text-sm">
                                    {{ $session->total_hm }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-8 italic">No clock records yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Approved By -->
            <div>
                <label class="block font-semibold text-gray-600 mb-1">Approved / Handled By</label>
                <input type="text" class="w-full border p-3 rounded bg-gray-50 text-lg" 
                       value="{{ $overtime->approver?->name ?? $overtime->approver?->username ?? 'Pending' }}" readonly>
            </div>
        </div>
    </div>

    <!-- BACK BUTTON -->
    <div class="mt-12 flex justify-center">
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center gap-3 bg-gray-200 text-gray-800 px-12 py-4 rounded-xl hover:bg-gray-300 transition-all font-bold text-lg shadow-md hover:shadow-lg">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>
</div>

@endsection