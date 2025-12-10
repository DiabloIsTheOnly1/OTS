@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white shadow-lg border rounded-2xl p-10 text-center">

        <!-- Icon: Green for Clock In, Blue for Clock Out -->
        <div class="flex justify-center mb-6">
            <div class="{{ $clock->clock_out ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }} p-6 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    @if ($clock->clock_out)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    @endif
                </svg>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-4xl font-bold mb-4
            {{ $clock->clock_out ? 'text-blue-700' : 'text-green-700' }}">
            {{ $message }}
        </h1>

        <!-- Time -->
        <p class="text-gray-700 text-lg mb-2">
            {{ $clock->clock_out ? 'Clock-out time:' : 'Clock-in time:' }}
        </p>
        <p class="text-3xl font-bold text-gray-900 mb-8">
            {{ $scannedAt->format('d M Y H:i:s') }}
        </p>

        <!-- Summary Box -->
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-left space-y-3 mb-10">
            <p class="text-lg"><strong>Name:</strong> {{ $overtime->staff->staff_name }}</p>
            <p class="text-lg"><strong>Date:</strong> {{ $overtime->date->format('d M Y') }}</p>
            @if ($clock->clock_out)
                <p class="text-lg"><strong>Total OT Time:</strong>
                    <span class="text-green-600 font-bold text-xl">{{ $clock->total_hm }}</span>
                </p>
            @endif
        </div>

        {{-- Back Button --}}
        <div class="text-center mt-6">
        <a href="{{ route('overtime.details', $overtime->id) }}" 
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-10 py-3.5 rounded-lg shadow transition">
           Back to Overtime Request Details
        </a>
    </div>
        </p>
    </div>
</div>
@endsection