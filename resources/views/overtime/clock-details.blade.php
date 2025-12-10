@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8 mt-8">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Overtime Clock Details</h2>
    </div>

    {{-- Employee & Date Info --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div>
            <p class="text-gray-500 text-xs uppercase tracking-wide">Employee Name</p>
            <p class="text-gray-900 font-semibold mt-1">{{ $overtime->staff->staff_name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500 text-xs uppercase tracking-wide">Overtime Date</p>
            <p class="text-gray-900 font-semibold mt-1">{{ $overtime->date->format('l, d F Y') }}</p>
        </div>
    </div>

    {{-- Clock Sessions --}}
    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-4">Clock Sessions</h3>

<div class="space-y-4 mb-6">
    @forelse($overtime->clockSessions as $session)
        <div class="flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-sm gap-4">
            <div class="flex-1">
                <p class="text-gray-500 text-xs uppercase">Clock In</p>
                <p class="text-gray-900 font-medium">{{ $session->clock_in ? $session->clock_in->format('H:i') : '—' }}</p>
            </div>
            <div class="flex-1">
                <p class="text-gray-500 text-xs uppercase">Clock Out</p>
                <p class="text-gray-900 font-medium">{{ $session->clock_out ? $session->clock_out->format('H:i') : '—' }}</p>
            </div>
        </div>
    @empty
        <p class="text-gray-500 text-center py-4">No clock sessions recorded.</p>
    @endforelse
</div>

@php
$totalRequested = $overtime->total_hours ?? 0; // decimal hours requested
$totalRequestedSeconds = $totalRequested * 3600; // convert requested hours to seconds

$totalSeconds = 0;

// Sum all completed clocks
foreach ($overtime->clocks as $clock) {
    if ($clock->clock_in) {
        $start = $clock->clock_in instanceof \Carbon\Carbon 
            ? $clock->clock_in 
            : \Carbon\Carbon::parse($clock->clock_in);

        $end = $clock->clock_out instanceof \Carbon\Carbon 
            ? $clock->clock_out 
            : ($clock->clock_out ? \Carbon\Carbon::parse($clock->clock_out) : now());

        $totalSeconds += $start->diffInSeconds($end);
    }
}

// Clocked time cannot exceed requested
$totalSeconds = min($totalSeconds, $totalRequestedSeconds);

// Remaining seconds
$remainingSeconds = max(0, $totalRequestedSeconds - $totalSeconds);

// Convert to HH:MM for display
$clockedH = floor($totalSeconds / 3600);
$clockedM = floor(($totalSeconds % 3600) / 60);
$clockedDisplay = sprintf('%02d:%02d', $clockedH, $clockedM);

$remainingH = floor($remainingSeconds / 3600);
$remainingM = floor(($remainingSeconds % 3600) / 60);
$remainingDisplay = sprintf('%02d:%02d', $remainingH, $remainingM);

// Progress percentage
$percent = $totalRequestedSeconds > 0 
    ? round(($totalSeconds / $totalRequestedSeconds) * 100) 
    : 0;
@endphp




    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
    <div>
        <p class="text-blue-800 font-semibold text-sm uppercase tracking-wide">Remaining Overtime Hours</p>
        <p class="text-gray-900 font-bold text-xl mt-1">{{ $remainingDisplay }} </p>
    </div>

        <div class="w-full sm:w-1/2">
        <div class="bg-gray-200 rounded-full h-4 overflow-hidden">
            <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
        </div>
        <p class="text-gray-500 text-xs mt-1 text-right">
            {{ $percent }}% Completed ({{ $clockedDisplay }} / {{ sprintf('%02d:%02d', floor($totalRequested), round(($totalRequested - floor($totalRequested)) * 60)) }})
        </p>
    </div>
</div>

    {{-- Back Button --}}
    <div class="text-center mt-6">
        <a href="{{ route('overtime.details', $overtime->id) }}" 
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-10 py-3.5 rounded-lg shadow transition">
           Back to Overtime Request Details
        </a>
    </div>
</div>
@endsection
