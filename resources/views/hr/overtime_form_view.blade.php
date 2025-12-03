@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto space-y-8 py-8 px-4 sm:px-6 lg:px-8">

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
                    <input type="text" class="w-full border p-2 rounded bg-gray-50" value="{{ $overtime->name }}" readonly>
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Position</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50" value="{{ $overtime->position }}" readonly>
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Employee ID / Reg No</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50 font-mono" 
                           value="{{ $overtime->reg_no ?? '-' }}" readonly>
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

                <!-- PLANNED OVERTIME SCHEDULE -->
                <div class="md:col-span-2 bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h3 class="font-bold text-amber-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        Planned Overtime Schedule
                    </h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-xs text-gray-600">Start Time</p>
                            <p class="font-bold text-lg text-amber-700">
                                {{ $overtime->start_time?->format('H:i') ?? '-' }}
                            </p>
                        </div>
                        <div class="flex items-center justify-center">
                            <span class="text-2xl text-amber-600">→</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">End Time</p>
                            <p class="font-bold text-lg text-amber-700">
                                {{ $overtime->end_time?->format('H:i') ?? '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <span class="inline-block bg-amber-700 text-white px-6 py-2 rounded-full font-bold text-lg">
                            {{ $overtime->total_hours ? number_format($overtime->total_hours, 2) . ' hours' : '-' }}
                        </span>
                    </div>
                </div>

                <!-- Work Details -->
                <div class="md:col-span-2">
                    <label class="block font-semibold text-gray-600 mb-1">Type of Work </label>
                    <textarea rows="3" class="w-full border p-3 rounded bg-gray-50 text-sm" readonly>{{ $overtime->reason }}</textarea>
                </div>

            </div>
        </div>

        <!-- BLOCK 2: Clock Sessions + Summary -->
        <div class="bg-white shadow border rounded-lg p-6 space-y-6">
            <h2 class="text-xl font-semibold Craftsman text-gray-800 border-b pb-3">Actual Clock Records</h2>

            <!-- Actual Total Hours -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 text-center">
                <p class="text-sm text-blue-600 font-semibold">Total Actual OT Hours</p>
                <p class="text-4xl font-bold text-blue-700 mt-2">
                    {{ $overtime->total_hm ?? '00:00' }}
                </p>
                @if($overtime->total_hours && $overtime->clocks->sum('total_time_taken') > 0)
                    @php
                        $actualSeconds = $overtime->clocks->sum('total_time_taken');
                        $plannedMinutes = $overtime->total_hours * 60;
                        $actualMinutes = $actualSeconds / 60;
                        $diff = $actualMinutes - $plannedMinutes;
                    @endphp
                    <p class="text-sm mt-3 {{ $diff > 0 ? 'text-red-600' : 'text-green-600' }}">
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
                <input type="text" class="w-full border p-2 rounded bg-gray-50" 
                       value="{{ $overtime->approver?->name ?? $overtime->approver?->username ?? 'Pending' }}" readonly>
            </div>
        </div>
    </div>

    <!-- BACK BUTTON -->
    <div class="mt-12 flex justify-center">
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center gap-3 bg-gray-200 text-gray-800 px-10 py-4 rounded-xl hover:bg-gray-300 active:bg-gray-400 transition-all font-bold text-base shadow-md hover:shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back
        </a>
    </div>
</div>

@endsection