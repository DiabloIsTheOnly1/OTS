@extends('layouts.app')

@section('content')

<div class="max-w-full mx-auto space-y-8 py-8 px-4 sm:px-6 lg:px-8">

    <h1 class="text-3xl font-bold text-blue-700 mb-8 text-center md:text-left">
        Overtime Request Details
    </h1>

    <!-- TWO BLOCKS SIDE-BY-SIDE ON DESKTOP, STACKED ON MOBILE -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- BLOCK 1: Main Info -->
        <div class="bg-white shadow border rounded-lg p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-800">Employee & Request Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Name -->
                <div>
                    <label class="block font-semibold mb-1">Name</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50"
                           value="{{ $overtime->name }}" readonly>
                </div>

                <!-- Position -->
                <div>
                    <label class="block font-semibold mb-1">Position</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50"
                           value="{{ $overtime->position }}" readonly>
                </div>

                <!-- Branch -->
                <div>
                    <label class="block font-semibold mb-1">Branch</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50"
                           value="{{ $overtime->branch?->name ?? '-' }}" readonly>
                </div>

                <!-- Department -->
                <div>
                    <label class="block font-semibold mb-1">Department</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50"
                           value="{{ $overtime->department?->department_name ?? '-' }}" readonly>
                </div>

                <!-- Date -->
                <div>
                    <label class="block font-semibold mb-1">Date</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50"
                           value="{{ $overtime->date->format('d M Y') }}" readonly>
                </div>

                <!-- Total Hours -->
                <div>
                    <label class="block font-semibold mb-1">Total OT Hours</label>
                    <input type="text" class="w-full border p-2 rounded font-bold text-blue-700 bg-blue-50"
                           value="{{ $overtime->total_hm }}" readonly>
                </div>

                <!-- Work done for the day -->
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-1">Work done for the day</label>
                    <textarea rows="4" class="w-full border p-2 rounded bg-gray-50" readonly>{{ $overtime->work_done }}</textarea>
                </div>

                <!-- Work to be completed -->
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-1">Work to be completed during OT</label>
                    <textarea rows="4" class="w-full border p-2 rounded bg-gray-50" readonly>{{ $overtime->reason }}</textarea>
                </div>

            </div>
        </div>

        <!-- BLOCK 2: Clock Sessions + Handled By -->
        <div class="bg-white shadow border rounded-lg p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-800">Clock Sessions & Approval</h2>

            <div class="grid grid-cols-1 gap-8">

                <!-- Clock Sessions -->
                <div>
                    <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider mb-3">Clock Sessions</p>
                    <div class="space-y-3 max-h-96 overflow-y-auto border rounded-lg p-4 bg-gray-50">
                        @forelse ($overtime->clocks as $session)
                            <div class="bg-white border rounded-lg p-4 flex justify-between items-center shadow-sm">
                                <div class="text-sm text-gray-700">
                                    <span class="font-semibold">Clock In:</span>
                                    {{ $session->clock_in?->format('H:i') ?? '-' }}<br>
                                    <span class="font-semibold">Clock Out:</span>
                                    {{ $session->clock_out?->format('H:i') ?? '-' }}
                                </div>
                                <span class="text-blue-600 font-bold text-sm bg-blue-100 px-4 py-2 rounded-full">
                                    {{ $session->total_hm }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm italic">No clock sessions recorded</p>
                        @endforelse
                    </div>
                </div>

                <!-- Handled By -->
                <div>
                    <label class="block font-semibold mb-1">Handled / Approved By</label>
                    <input type="text" class="w-full border p-2 rounded bg-gray-50"
                           value="{{ $overtime->approver?->username ?? '-' }}" readonly>
                </div>

            </div>
        </div>

    </div>

    <!-- BACK BUTTON -->
        <div class="mt-12 flex justify-center">
            <a href="{{ url()->previous() }}"
            class="inline-flex items-center gap-3 bg-gray-200 text-gray-800 px-10 py-4 rounded-xl hover:bg-gray-300 active:bg-gray-400 transition-all font-bold text-base shadow-md hover:shadow-lg">
                
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                
                Back
            </a>
        </div>

</div>

@endsection