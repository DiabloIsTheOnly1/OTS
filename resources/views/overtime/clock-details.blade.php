@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-6">
    <h2 class="text-xl font-bold text-gray-800 mb-8 text-center">Clock Details</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm mb-6">
        <div>
            <p class="text-gray-600 text-xs">Name</p>
            <p class="font-medium text-gray-900 mt-1">{{ $overtime->staff->staff_name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-600 text-xs">Overtime Date</p>
            <p class="font-medium text-gray-900 mt-1">{{ $overtime->date->format('l, d F Y') }}</p>
        </div>
    </div>

    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Clock Sessions</h3>
    <div class="space-y-3 mb-6">
        @forelse($overtime->clockSessions as $session)
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex justify-between">
                <p>Clock In: {{ $session->clock_in ? $session->clock_in->format('H:i') : '—' }}</p>
                <p>Clock Out: {{ $session->clock_out ? $session->clock_out->format('H:i') : '—' }}</p>
                <p>Hours: {{ $session->hours ?? '—' }}</p>
            </div>
        @empty
            <p class="text-gray-500">No clock sessions recorded.</p>
        @endforelse
    </div>

    <p class="text-gray-700 font-medium">
        Remaining OT Hours: <span class="text-blue-600">{{ $remainingHours }}h</span>
    </p>

    <div class="mt-6 text-center">
        <a href="{{ route('overtime.clock.details', $overtime->id) }}"
           class="text-gray-600 hover:underline text-sm">
            Back to Overtime Request
        </a>
    </div>
</div>
@endsection
